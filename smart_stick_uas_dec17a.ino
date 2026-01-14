// smart_stick_uas_dec17a.ino - DENGAN PATTERN BUZZER FIXED
#include <ESP8266WiFi.h>
#include <WiFiClient.h>
#include <ESP8266HTTPClient.h>
#include "thingProperties.h"

// Pin Definitions
#define TRIG1_PIN 5     // GPIO5 (D1)
#define ECHO1_PIN 4     // GPIO4 (D2)
#define TRIG2_PIN 0     // GPIO0 (D3)
#define ECHO2_PIN 2     // GPIO2 (D4)
#define TOUCH_PIN 12    // GPIO12 (D6)
#define BUZZER1_PIN 14  // GPIO14 (D5)
#define BUZZER2_PIN 13  // GPIO13 (D7)
#define SOIL_PIN 16     // GPIO16 (D0)

// Variables
unsigned long lastSendTime = 0;
const unsigned long sendInterval = 2000;
bool systemEnabled = true;
bool lastTouchState = false;
bool currentTouchState = false;

// Variabel terpisah untuk pattern buzzer 2
unsigned long lastBuzzer2SoilTime = 0;  // Untuk soil moisture
unsigned long lastBuzzer2UltraTime = 0; // Untuk ultrasonic 2
bool buzzer2SoilState = false;          // State untuk soil
bool buzzer2UltraState = false;         // State untuk ultrasonic 2

// Server Configuration
const char* serverUrl = "http://your-ipconfig/smart_stick/api.php";

// Debug mode aktif
bool debugMode = true;

void setup() {
  Serial.begin(115200);
  delay(1000);
  
  Serial.println("\n==================================");
  Serial.println("      SMART STICK SYSTEM");
  Serial.println("      BUZZER PATTERN FIXED");
  Serial.println("==================================");
  Serial.println("Buzzer Patterns:");
  Serial.println("- Soil Wet: 1s ON, 1s OFF");
  Serial.println("- Ultrasonic2: 2s ON, 1s OFF");
  Serial.println("==================================");
  
  // Initialize pins
  pinMode(TRIG1_PIN, OUTPUT);
  pinMode(ECHO1_PIN, INPUT);
  pinMode(TRIG2_PIN, OUTPUT);
  pinMode(ECHO2_PIN, INPUT);
  pinMode(TOUCH_PIN, INPUT);
  pinMode(BUZZER1_PIN, OUTPUT);
  pinMode(BUZZER2_PIN, OUTPUT);
  pinMode(SOIL_PIN, INPUT_PULLUP);
  
  // Default state
  digitalWrite(BUZZER1_PIN, LOW);
  digitalWrite(BUZZER2_PIN, LOW);
  digitalWrite(TRIG1_PIN, LOW);
  digitalWrite(TRIG2_PIN, LOW);
  
  Serial.println("\n[SETUP] Starting Arduino Cloud...");
  initProperties();
  
  // Connect to WiFi manually
  Serial.print("[SETUP] Connecting to WiFi: ");
  Serial.println(SECRET_SSID);
  
  WiFi.begin(SECRET_SSID, SECRET_OPTIONAL_PASS);
  int wifiTimeout = 0;
  while (WiFi.status() != WL_CONNECTED && wifiTimeout < 20) {
    delay(500);
    Serial.print(".");
    wifiTimeout++;
  }
  
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\n[SETUP] WiFi Connected!");
    Serial.print("[SETUP] IP: ");
    Serial.println(WiFi.localIP());
    Serial.print("[SETUP] RSSI: ");
    Serial.println(WiFi.RSSI());
    
    // Test server connection
    testServerConnection();
  } else {
    Serial.println("\n[SETUP] WiFi Failed!");
  }
  
  // Start Arduino Cloud
  ArduinoCloud.begin(ArduinoIoTPreferredConnection);
  setDebugMessageLevel(2);
  
  // Startup beep
  digitalWrite(BUZZER1_PIN, HIGH);
  delay(300);
  digitalWrite(BUZZER1_PIN, LOW);
  
  Serial.println("\n[SETUP] System Ready!");
}

void loop() {
  ArduinoCloud.update();
  
  // Read touch sensor
  currentTouchState = digitalRead(TOUCH_PIN);
  
  // Toggle system
  if (currentTouchState && !lastTouchState) {
    systemEnabled = !systemEnabled;
    Serial.print("\n[TOUCH] System: ");
    Serial.println(systemEnabled ? "ON" : "OFF");
    
    digitalWrite(BUZZER1_PIN, HIGH);
    delay(1000);
    digitalWrite(BUZZER1_PIN, LOW);
    
    systemON = systemEnabled;
  }
  lastTouchState = currentTouchState;
  
  if (systemEnabled) {
    // Read sensors
    int dist1 = readDistance(TRIG1_PIN, ECHO1_PIN);
    int dist2 = readDistance(TRIG2_PIN, ECHO2_PIN);
    bool soilIsWet = digitalRead(SOIL_PIN) == LOW;
    
    // Update Cloud
    distance1 = dist1;
    distance2 = dist2;
    soilWet = soilIsWet ? 1 : 0;
    
    // Print sensor values
    static unsigned long lastPrint = 0;
    if (millis() - lastPrint >= 3000) {
      Serial.print("\n[SENSORS] D1: ");
      Serial.print(dist1);
      Serial.print("cm | D2: ");
      Serial.print(dist2);
      Serial.print("cm | Soil: ");
      Serial.print(soilIsWet ? "WET" : "DRY");
      Serial.print(" | System: ");
      Serial.println(systemEnabled ? "ON" : "OFF");
      lastPrint = millis();
    }
    
    // Process logic dengan pattern baru
    processSensorLogic(dist1, dist2, soilIsWet);
    
    // Send to server
    if (millis() - lastSendTime >= sendInterval) {
      if (WiFi.status() == WL_CONNECTED) {
        sendToServer(dist1, dist2, soilIsWet, systemEnabled);
      } else {
        Serial.println("[ERROR] WiFi disconnected!");
        WiFi.reconnect();
      }
      lastSendTime = millis();
    }
  } else {
    // System OFF - matikan semua buzzer
    digitalWrite(BUZZER1_PIN, LOW);
    digitalWrite(BUZZER2_PIN, LOW);
    
    // Reset states
    buzzer2SoilState = false;
    buzzer2UltraState = false;
    
    distance1 = 0;
    distance2 = 0;
    soilWet = 0;
    
    static unsigned long lastOffPrint = 0;
    if (millis() - lastOffPrint >= 5000) {
      Serial.println("[SYSTEM] OFF - waiting for touch...");
      lastOffPrint = millis();
    }
  }
  
  delay(50); // Delay kecil untuk stabilitas
}

int readDistance(int trigPin, int echoPin) {
  digitalWrite(trigPin, LOW);
  delayMicroseconds(2);
  digitalWrite(trigPin, HIGH);
  delayMicroseconds(10);
  digitalWrite(trigPin, LOW);
  
  long duration = pulseIn(echoPin, HIGH, 30000);
  int distance = duration * 0.034 / 2;
  
  if (distance <= 0 || distance > 400) {
    return 999;
  }
  return distance;
}

void processSensorLogic(int dist1, int dist2, bool soilIsWet) {
  // Reset buzzers terlebih dahulu
  digitalWrite(BUZZER1_PIN, LOW);
  
  // Priority 1: Soil Moisture (HIGHEST PRIORITY)
  if (soilIsWet) {
    // Pattern untuk Soil: 1 detik ON, 1 detik OFF
    if (millis() - lastBuzzer2SoilTime >= 1000) {
      buzzer2SoilState = !buzzer2SoilState;
      digitalWrite(BUZZER2_PIN, buzzer2SoilState);
      lastBuzzer2SoilTime = millis();
      
      if (debugMode) {
        if (buzzer2SoilState) {
          Serial.println("[BUZZER2] 🌊 SOIL WET: ON (1 second)");
        } else {
          Serial.println("[BUZZER2] 🌊 SOIL WET: OFF (1 second)");
        }
      }
    }
    // Reset ultrasonic pattern karena soil memiliki prioritas
    buzzer2UltraState = false;
    return; // Keluar dari fungsi, soil memiliki prioritas tertinggi
  }
  
  // Priority 2: HC-SR04 #1 (distance ≤ 50cm)
  if (dist1 <= 50 && dist1 > 0 && dist1 != 999) {
    digitalWrite(BUZZER1_PIN, HIGH);
    if (debugMode) {
      static unsigned long lastBuzzer1Log = 0;
      if (millis() - lastBuzzer1Log >= 1000) {
        Serial.println("[BUZZER1] 📡 ULTRASONIC1: CONTINUOUS ON (≤50cm)");
        lastBuzzer1Log = millis();
      }
    }
  }
  
  // Priority 3: HC-SR04 #2 (distance ≤ 20cm)
  if (dist2 <= 20 && dist2 > 0 && dist2 != 999) {
    // Pattern untuk Ultrasonic 2: 2 detik ON, 1 detik OFF
    unsigned long patternTime = buzzer2UltraState ? 2000 : 1000; // ON: 2s, OFF: 1s
    
    if (millis() - lastBuzzer2UltraTime >= patternTime) {
      buzzer2UltraState = !buzzer2UltraState;
      digitalWrite(BUZZER2_PIN, buzzer2UltraState);
      lastBuzzer2UltraTime = millis();
      
      if (debugMode) {
        if (buzzer2UltraState) {
          Serial.println("[BUZZER2] 📡 ULTRASONIC2: ON (2 seconds)");
        } else {
          Serial.println("[BUZZER2] 📡 ULTRASONIC2: OFF (1 second)");
        }
      }
    }
  } else {
    // Matikan buzzer2 jika ultrasonic 2 tidak dalam range
    buzzer2UltraState = false;
    digitalWrite(BUZZER2_PIN, LOW);
  }
}

void sendToServer(int dist1, int dist2, bool soilIsWet, bool systemOn) {
  if (WiFi.status() != WL_CONNECTED) {
    if (debugMode) Serial.println("[SERVER] WiFi not connected");
    return;
  }
  
  HTTPClient http;
  WiFiClient client;
  
  // Create simple form data
  String postData = "distance1=" + String(dist1) + 
                    "&distance2=" + String(dist2) + 
                    "&soilWet=" + String(soilIsWet ? "1" : "0") + 
                    "&systemON=" + String(systemOn ? "1" : "0");
  
  if (debugMode) {
    static unsigned long lastServerLog = 0;
    if (millis() - lastServerLog >= 5000) {
      Serial.println("[SERVER] Sending data...");
      Serial.println("[SERVER] Data: " + postData);
      lastServerLog = millis();
    }
  }
  
  http.begin(client, serverUrl);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  
  int httpCode = http.POST(postData);
  
  if (httpCode > 0) {
    if (debugMode && httpCode != 200) {
      Serial.print("[SERVER] Response code: ");
      Serial.println(httpCode);
    }
  } else {
    if (debugMode) {
      Serial.print("[SERVER] Error: ");
      Serial.println(http.errorToString(httpCode).c_str());
    }
  }
  
  http.end();
}

void testServerConnection() {
  Serial.println("\n[SERVER] Testing connection...");
  
  HTTPClient http;
  WiFiClient client;
  
  http.begin(client, "http://your-ipconfig/smart_stick/api.php");
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  
  // Test dengan data dummy
  String testData = "distance1=100&distance2=200&soilWet=0&systemON=1";
  int httpCode = http.POST(testData);
  
  if (httpCode > 0) {
    Serial.print("[SERVER TEST] Response: ");
    Serial.println(httpCode);
    String response = http.getString();
    if (response.length() < 100) {
      Serial.print("[SERVER TEST] Body: ");
      Serial.println(response);
    }
  } else {
    Serial.print("[SERVER TEST] Failed: ");
    Serial.println(http.errorToString(httpCode).c_str());
  }
  
  http.end();
}

void onSystemONChange() {
  systemEnabled = systemON;
  if (debugMode) {
    Serial.print("[CLOUD] System changed to: ");
    Serial.println(systemON ? "ON" : "OFF");
  }
}