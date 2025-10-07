#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// WiFi credentials
const char* ssid = "YOUR_WIFI_SSID";
const char* password = "YOUR_WIFI_PASSWORD";

// Server details
const char* serverUrl = "http://your-server-ip/api/watering-control";

// Pin definitions
const int relayPin = 23;  // GPIO pin for relay control
const int wateringDuration = 60000; // 1 minute in milliseconds

// Variables
unsigned long wateringStartTime = 0;
bool isWatering = false;

void setup() {
  Serial.begin(115200);
  
  // Initialize relay pin
  pinMode(relayPin, OUTPUT);
  digitalWrite(relayPin, LOW);  // Ensure valve is closed initially
  
  // Connect to WiFi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi...");
  }
  Serial.println("Connected to WiFi");
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverUrl);
    int httpCode = http.GET();
    
    if (httpCode == HTTP_CODE_OK) {
      String payload = http.getString();
      StaticJsonDocument<200> doc;
      deserializeJson(doc, payload);
      
      bool shouldWater = doc["should_water"];
      
      if (shouldWater && !isWatering) {
        startWatering();
      }
    }
    http.end();
  }
  
  // Check if watering duration has elapsed
  if (isWatering && (millis() - wateringStartTime >= wateringDuration)) {
    stopWatering();
  }
  
  delay(5000); // Check every 5 seconds
}

void startWatering() {
  digitalWrite(relayPin, HIGH);  // Open valve
  wateringStartTime = millis();
  isWatering = true;
  Serial.println("Watering started");
}

void stopWatering() {
  digitalWrite(relayPin, LOW);  // Close valve
  isWatering = false;
  Serial.println("Watering stopped");
} 