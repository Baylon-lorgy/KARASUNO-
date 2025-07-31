#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>
#include "DHT.h"
#include <EEPROM.h>
#include <WiFiUdp.h>
#include <NTPClient.h>
#include <vector>

// WiFi credentials
const char* ssid = "PLDTHOMEFIBRc2990";
const char* password = "PLDTWIFIpu4vh";

// Server URL configuration
#define EEPROM_SIZE 128
#define SERVER_URL_ADDR 0
char sensorDataUrl[100] = "http://192.168.1.6:8000/api/sensor-data/update";
char sensorReadingsUrl[100] = "http://192.168.1.6:8000/api/sensor-readings";
char wateringStatusUrl[100] = "http://192.168.1.6:8000/api/watering-status";
char wateringSchedulesUrl[100] = "http://192.168.1.6:8000/api/watering-schedules";

// Sensor pin definitions
#define WATER_SENSOR_PIN 34        // Non-float water sensor
#define WATER_FLOAT_SENSOR_PIN 12  // Water float sensor on GPIO12
#define SOIL_MOISTURE_PIN 35
#define DHTPIN 4                   // GPIO 4 for DHT11 data
#define DHTTYPE DHT11             
#define RELAY_PIN 23              // Relay pin for solenoid valve

// Timing constants
const unsigned long SENSOR_READ_INTERVAL = 30000;     // 30 seconds
const unsigned long WATERING_CHECK_INTERVAL = 5000;   // 5 seconds for watering checks
const unsigned long WIFI_TIMEOUT_MS = 20000;
const unsigned long WIFI_RECOVER_TIME_MS = 30000;
const unsigned long DEBUG_INTERVAL = 5000;            // Debug print interval
const unsigned long VALVE_SAFETY_CHECK_INTERVAL = 60000; // Check valve state every minute

// Global variables
unsigned long lastSensorReadTime = 0;
unsigned long lastWateringCheckTime = 0;
unsigned long lastDebugTime = 0;
unsigned long lastValveSafetyCheck = 0;
bool isWatering = false;
bool waterFloatStatus = false;
bool lastWateringState = false;
unsigned long wateringStartTime = 0;
unsigned long wateringDuration = 0;
unsigned long lastSuccessfulWateringCheck = 0;
int failedWateringChecks = 0;
const int MAX_FAILED_CHECKS = 3;

// Create DHT object
DHT dht(DHTPIN, DHTTYPE);

// Inverted relay logic - relay is active LOW
#define VALVE_OPEN LOW      // Relay ON (active LOW) = Valve OPEN
#define VALVE_CLOSED HIGH   // Relay OFF = Valve CLOSED

// Function declarations
bool connectToWiFi();
void readAndSendSensorData();
void checkWateringStatus();
void printDebugInfo();
void performValveSafetyCheck();
void emergencyValveClose();
void controlValve(bool open);
void fetchSchedules();
String getCurrentTimeString();
int getCurrentDayOfYear();
void checkAndTriggerScheduledWatering();

WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP, "pool.ntp.org", 8 * 3600, 60000); // UTC+8 for Manila, update every 60s

// Update Schedule struct
typedef struct {
  String time; // "08:00"
  int duration; // minutes
  std::vector<int> daysOfWeek; // e.g. [1,3,5]
  int lastTriggeredDay; // day of year
} Schedule;

std::vector<Schedule> schedules;

unsigned long lastScheduleFetch = 0;
const unsigned long SCHEDULE_FETCH_INTERVAL = 5 * 60 * 1000; // 5 minutes
unsigned long lastScheduleCheck = 0;
const unsigned long SCHEDULE_CHECK_INTERVAL = 60 * 1000; // 1 minute

void setup() {
  Serial.begin(115200);
  Serial.println("\n=== Rainwater System Initialization ===");
  
  // Initialize pins
  pinMode(RELAY_PIN, OUTPUT);
  controlValve(false);  // Start with valve CLOSED
  delay(1000); // Give relay time to settle
  
  // Verify initial relay state
  int relayState = digitalRead(RELAY_PIN);
  Serial.printf("Initial relay state: %s (Pin %d = %d)\n", 
                relayState == VALVE_OPEN ? "OPEN" : "CLOSED",
                RELAY_PIN, relayState);
  
  pinMode(WATER_FLOAT_SENSOR_PIN, INPUT_PULLUP);
  
  // Initialize WiFi
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  
  // Initialize variables
  lastSensorReadTime = 0;
  lastWateringCheckTime = 0;
  lastDebugTime = 0;
  lastValveSafetyCheck = 0;
  isWatering = false;
  
  // Force valve closed on startup
  controlValve(false);
  delay(1000);
  Serial.println("Valve CLOSED on startup");

  timeClient.begin();
  timeClient.update();
}

void controlValve(bool open) {
  digitalWrite(RELAY_PIN, open ? VALVE_OPEN : VALVE_CLOSED);
  Serial.printf("Valve %s\n", open ? "OPENED" : "CLOSED");
}

bool connectToWiFi() {
  Serial.println("\n=== WiFi Connection Attempt ===");
  Serial.printf("Connecting to: %s\n", ssid);
  
  WiFi.mode(WIFI_STA);
  WiFi.disconnect();
  delay(100);
  
  WiFi.begin(ssid, password);
  
  unsigned long startAttemptTime = millis();
  int attempts = 0;
  
  while (WiFi.status() != WL_CONNECTED && 
         millis() - startAttemptTime < WIFI_TIMEOUT_MS) {
    Serial.printf("Attempt %d: Connecting...\n", ++attempts);
    delay(500);
  }
  
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nWiFi Connected Successfully!");
    Serial.printf("Local IP: %s\n", WiFi.localIP().toString().c_str());
    Serial.printf("Signal Strength (RSSI): %d dBm\n", WiFi.RSSI());
    return true;
  } else {
    Serial.println("\nWiFi Connection FAILED!");
    Serial.printf("Error Code: %d\n", WiFi.status());
    return false;
  }
}

void readAndSendSensorData() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("ERROR: WiFi not connected. Cannot send sensor data.");
    return;
  }

  Serial.println("\n=== Sensor Reading Cycle ===");
  
  // Read water and soil values
  int waterLevel = analogRead(WATER_SENSOR_PIN);
  int soilMoisture = analogRead(SOIL_MOISTURE_PIN);
  waterFloatStatus = digitalRead(WATER_FLOAT_SENSOR_PIN) == LOW;

  // Map to percentages (inverted for soil moisture)
  waterLevel = map(waterLevel, 0, 4095, 0, 100);
  soilMoisture = map(soilMoisture, 4095, 0, 0, 100);  // Inverted mapping

  // Read temperature and humidity
  float humidity = dht.readHumidity();
  float temperature = dht.readTemperature();

  if (isnan(humidity) || isnan(temperature)) {
    Serial.println("WARNING: DHT11 reading failed!");
    humidity = 0;
    temperature = 0;
  }

  // Print processed readings
  Serial.println("\nProcessed Sensor Values:");
  Serial.printf("- Water Level: %d%%\n", waterLevel);
  Serial.printf("- Soil Moisture: %d%%\n", soilMoisture);
  Serial.printf("- Temperature: %.1f°C\n", temperature);
  Serial.printf("- Humidity: %.1f%%\n", humidity);
  Serial.printf("- Float Status: %s\n", waterFloatStatus ? "Water Detected" : "No Water");

  // Send data to server
  HTTPClient http;
  Serial.printf("\nConnecting to: %s\n", sensorDataUrl);
  http.begin(sensorDataUrl);
  http.addHeader("Content-Type", "application/json");
  http.addHeader("Accept", "application/json");

  StaticJsonDocument<256> doc;
  doc["water_level"] = waterLevel;
  doc["soil_moisture"] = soilMoisture;
  doc["temperature"] = temperature;
  doc["humidity"] = humidity;
  doc["water_float_status"] = waterFloatStatus ? "water_detected" : "no_water";
  doc["timestamp"] = millis();  // Add timestamp for tracking

  String jsonString;
  serializeJson(doc, jsonString);
  Serial.printf("Sending JSON: %s\n", jsonString.c_str());

  int httpResponseCode = http.POST(jsonString);
  
  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.printf("Sensor data sent successfully (HTTP %d)\n", httpResponseCode);
    Serial.printf("Response: %s\n", response.c_str());

    // If successful (HTTP 200), store in sensor_readings
    if (httpResponseCode == 200) {
      // Send to sensor_readings endpoint
      HTTPClient http2;
      http2.begin(sensorReadingsUrl);  // Use the dedicated URL for sensor readings
      http2.addHeader("Content-Type", "application/json");
      http2.addHeader("Accept", "application/json");

      // Add HTTP status and response to the data
      doc["http_status"] = httpResponseCode;
      doc["response"] = response;

      String jsonString2;
      serializeJson(doc, jsonString2);
      
      int storeResult = http2.POST(jsonString2);
      if (storeResult > 0) {
        Serial.println("Reading stored in sensor_readings collection");
      } else {
        Serial.printf("Failed to store in sensor_readings: %d\n", storeResult);
      }
      http2.end();
    }
  } else {
    Serial.printf("Failed to send sensor data (HTTP %d)\n", httpResponseCode);
    Serial.printf("Error: %s\n", http.errorToString(httpResponseCode).c_str());
    Serial.println("Check if the server IP and port are correct and the server is running.");
  }

  http.end();
}

void performValveSafetyCheck() {
  // Check if valve state matches isWatering
  bool currentValveState = digitalRead(RELAY_PIN);
  bool expectedValveState = isWatering ? VALVE_OPEN : VALVE_CLOSED;
  
  if (currentValveState != expectedValveState) {
    Serial.println("Valve safety check: Correcting valve state");
    controlValve(isWatering);
  }
}

void emergencyValveClose() {
  Serial.println("EMERGENCY: Closing valve due to communication failure");
  controlValve(false);
  isWatering = false;
  wateringStartTime = 0;
  wateringDuration = 0;
}

void checkWateringStatus() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("ERROR: WiFi not connected. Cannot check watering status.");
    failedWateringChecks++;
    if (failedWateringChecks >= MAX_FAILED_CHECKS) {
      emergencyValveClose();
    }
    return;
  }

  // Check if watering timer has expired
  if (isWatering && wateringStartTime > 0) {
    unsigned long currentTime = millis();
    if (currentTime - wateringStartTime >= wateringDuration) {
      Serial.println("Watering duration completed. Closing valve...");
      controlValve(false);
      isWatering = false;
      wateringStartTime = 0;
      wateringDuration = 0;
      return;
    }
  }

  HTTPClient http;
  Serial.printf("\nChecking watering status at: %s\n", wateringStatusUrl);
  http.begin(wateringStatusUrl);
  http.addHeader("Accept", "application/json");

  int httpResponseCode = http.GET();
  
  if (httpResponseCode > 0) {
    String response = http.getString();
    Serial.printf("Got watering status (HTTP %d): %s\n", httpResponseCode, response.c_str());

    StaticJsonDocument<200> doc;
    DeserializationError error = deserializeJson(doc, response);

    if (!error) {
      bool shouldWater = doc["should_water"];
      int duration = doc["duration"] ? doc["duration"].as<int>() : 0;

      Serial.printf("Should water (from server): %s\n", shouldWater ? "YES" : "NO");
      Serial.printf("Duration: %d minutes\n", duration);

      // should_water == false means "start watering"
      if (!shouldWater && !isWatering && duration > 0) {
        wateringDuration = (unsigned long)duration * 60 * 1000;
        wateringStartTime = millis();
        isWatering = true;
        controlValve(true);
        Serial.printf("Starting scheduled watering for %d minutes\n", duration);
        failedWateringChecks = 0;
        lastSuccessfulWateringCheck = millis();
      }
      // should_water == true means "stop watering"
      else if (shouldWater && isWatering) {
        controlValve(false);
        isWatering = false;
        wateringStartTime = 0;
        wateringDuration = 0;
        Serial.println("Stopping watering (should_water is true)");
        failedWateringChecks = 0;
        lastSuccessfulWateringCheck = millis();
      }
    } else {
      Serial.printf("Failed to parse JSON: %s\n", error.c_str());
      failedWateringChecks++;
    }
  } else {
    Serial.printf("Failed to get watering status (HTTP %d)\n", httpResponseCode);
    Serial.printf("Error: %s\n", http.errorToString(httpResponseCode).c_str());
    failedWateringChecks++;
  }

  http.end();

  // Emergency check: If we haven't had a successful check in 5 minutes, close the valve
  if (millis() - lastSuccessfulWateringCheck > 300000) { // 5 minutes
    emergencyValveClose();
  }
}

void printDebugInfo() {
  Serial.println("\n=== System Status ===");
  Serial.printf("WiFi: %s (RSSI: %d dBm)\n", 
                WiFi.status() == WL_CONNECTED ? "Connected" : "Disconnected",
                WiFi.RSSI());
  Serial.printf("Watering: %s\n", isWatering ? "Active" : "Inactive");
  Serial.printf("Valve State: %s (Pin %d)\n", 
                digitalRead(RELAY_PIN) == VALVE_OPEN ? "OPEN" : "CLOSED", 
                RELAY_PIN);
  Serial.printf("Water Available: %s\n", waterFloatStatus ? "Yes" : "No");
  Serial.printf("Last Watering State: %s\n", lastWateringState ? "Active" : "Inactive");
}

void loop() {
  unsigned long currentTime = millis();
  
  // WiFi check
  if (WiFi.status() != WL_CONNECTED) {
    if (!connectToWiFi()) {
      delay(WIFI_RECOVER_TIME_MS);
      return;
    }
  }
  
  // Regular sensor readings
  if (currentTime - lastSensorReadTime >= SENSOR_READ_INTERVAL) {
    readAndSendSensorData();
    lastSensorReadTime = currentTime;
  }
  
  // Check watering commands more frequently
  if (currentTime - lastWateringCheckTime >= WATERING_CHECK_INTERVAL) {
    checkWateringStatus();
    lastWateringCheckTime = currentTime;
  }
  
  // Valve safety check
  if (currentTime - lastValveSafetyCheck >= VALVE_SAFETY_CHECK_INTERVAL) {
    performValveSafetyCheck();
    lastValveSafetyCheck = currentTime;
  }
  
  // Debug information
  if (currentTime - lastDebugTime >= DEBUG_INTERVAL) {
    printDebugInfo();
    lastDebugTime = currentTime;
  }

  // Fetch schedules every 5 minutes
  if (currentTime - lastScheduleFetch >= SCHEDULE_FETCH_INTERVAL) {
    fetchSchedules();
    lastScheduleFetch = currentTime;
  }
  // Check schedules every minute
  if (currentTime - lastScheduleCheck >= SCHEDULE_CHECK_INTERVAL) {
    checkAndTriggerScheduledWatering();
    lastScheduleCheck = currentTime;
  }

  // Debug: print current time and day every minute
  static unsigned long lastTimePrint = 0;
  if (currentTime - lastTimePrint >= 60000) {
    Serial.printf("[Time] ESP32 time: %s, day: %d\n", getCurrentTimeString().c_str(), getCurrentDayOfWeek());
    lastTimePrint = currentTime;
  }
}

void fetchSchedules() {
  if (WiFi.status() != WL_CONNECTED) return;
  HTTPClient http;
  http.begin(wateringSchedulesUrl);
  int httpResponseCode = http.GET();
  if (httpResponseCode == 200) {
    String response = http.getString();
    StaticJsonDocument<4096> doc;
    DeserializationError error = deserializeJson(doc, response);
    if (!error) {
      schedules.clear();
      Serial.printf("[Schedule] Fetched schedules at %s\n", getCurrentTimeString().c_str());
      for (JsonObject sch : doc.as<JsonArray>()) {
        Schedule s;
        s.time = String((const char*)sch["start_time"]);
        s.duration = sch["duration"];
        s.lastTriggeredDay = -1;
        s.daysOfWeek.clear();
        Serial.printf("  - Schedule: %s, duration: %d, days: ", s.time.c_str(), s.duration);
        for (JsonVariant d : sch["days_of_week"].as<JsonArray>()) {
          s.daysOfWeek.push_back((int)d);
          Serial.printf("%d ", (int)d);
        }
        Serial.println();
        schedules.push_back(s);
      }
      Serial.printf("[Schedule] Total: %d\n", schedules.size());
    }
  }
  http.end();
}

String getCurrentTimeString() {
  timeClient.update();
  int h = timeClient.getHours();
  int m = timeClient.getMinutes();
  char buf[6];
  sprintf(buf, "%02d:%02d", h, m);
  return String(buf);
}

int getCurrentDayOfYear() {
  timeClient.update();
  time_t raw = timeClient.getEpochTime();
  struct tm * ti = gmtime(&raw);
  return ti->tm_yday;
}

// Helper to get current day of week (0=Sun, 6=Sat)
int getCurrentDayOfWeek() {
  timeClient.update();
  time_t raw = timeClient.getEpochTime();
  struct tm * ti = gmtime(&raw);
  return ti->tm_wday;
}

void checkAndTriggerScheduledWatering() {
  String nowTime = getCurrentTimeString();
  int today = getCurrentDayOfWeek(); // 0=Sun, 6=Sat
  int dayOfYear = getCurrentDayOfYear();
  Serial.printf("[Schedule] Checking at %s, day: %d\n", nowTime.c_str(), today);
  for (auto &sch : schedules) {
    // Only trigger if today is in daysOfWeek
    bool todayScheduled = false;
    for (int d : sch.daysOfWeek) {
      if (d == today) { todayScheduled = true; break; }
    }
    Serial.printf("  - Schedule: %s, duration: %d, days: ", sch.time.c_str(), sch.duration);
    for (int d : sch.daysOfWeek) Serial.printf("%d ", d);
    Serial.printf("| todayScheduled: %s\n", todayScheduled ? "YES" : "NO");
    if (sch.time == nowTime && todayScheduled && sch.lastTriggeredDay != dayOfYear) {
      Serial.printf("[Schedule] Triggering watering at %s for %d min (days: ", sch.time.c_str(), sch.duration);
      for (int d : sch.daysOfWeek) Serial.printf("%d ", d);
      Serial.println(")");
      if (!isWatering) {
        wateringDuration = (unsigned long)sch.duration * 60 * 1000;
        wateringStartTime = millis();
        isWatering = true;
        controlValve(true);
      }
      sch.lastTriggeredDay = dayOfYear;
    }
  }
} 