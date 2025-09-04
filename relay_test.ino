#define RELAY_PIN 23

void setup() {
  Serial.begin(115200);
  pinMode(RELAY_PIN, OUTPUT);
  digitalWrite(RELAY_PIN, LOW);  // Start with relay OFF
  Serial.println("Relay Test Program");
  Serial.println("Relay should click every 2 seconds");
}

void loop() {
  // Turn relay ON
  digitalWrite(RELAY_PIN, HIGH);
  Serial.println("Relay ON");
  delay(2000);  // Wait 2 seconds
  
  // Turn relay OFF
  digitalWrite(RELAY_PIN, LOW);
  Serial.println("Relay OFF");
  delay(2000);  // Wait 2 seconds
} 