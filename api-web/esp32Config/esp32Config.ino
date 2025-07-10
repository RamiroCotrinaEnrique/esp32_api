// Instalar esta librería
#include <WiFi.h>
#include <HTTPClient.h>
#include <DHT11.h>
#include <ArduinoJson.h>  

//const char* ssid = "Red A52s";
//const char* password = "12345678";

const char* ssid = "AVILA";
const char* password = "46185953";
const char* api_url = "https://ucv.vcodesystems.com/api-web/index.php";

DHT11 dht11(18);
int ledVerde = 26;
int ledRojo = 27;
int sensorMQ7 = 34;

void setup() {
  Serial.begin(115200);
  pinMode(ledVerde, OUTPUT);
  pinMode(ledRojo, OUTPUT);

  WiFi.begin(ssid, password);
  Serial.print("Conectando a WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi conectado");
}

void loop() {
  
  int temperatura = 0;
  int humedad = 0;
  int sensorMQ7read = analogRead(sensorMQ7);

  int resultado = dht11.readTemperatureHumidity(temperatura, humedad);

  if (resultado == 0) {
    Serial.print("Temperature: ");
    Serial.print(temperatura);
    Serial.print(" °C\tHumidity: ");
    Serial.print(humedad);
    Serial.println(" %");    
  } else {
    Serial.println(DHT11::getErrorString(resultado));
  }

  Serial.print("Gas Sensor: ");
  Serial.print(sensorMQ7read);
  Serial.print("\t\t");

  if (sensorMQ7read > 1800) {
    Serial.println("Gas");
    digitalWrite(ledRojo, HIGH);
    digitalWrite(ledVerde, LOW);
  } else {
    Serial.println("No Gas");
    digitalWrite(ledVerde, HIGH);
    digitalWrite(ledRojo, LOW);
  }

  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(api_url);
    
    // Cambiamos el Content-Type a application/json
    http.addHeader("Content-Type", "application/json");
    
    // Creamos un objeto JSON
    StaticJsonDocument<200> doc;
    doc["temperatura"] = temperatura;
    doc["humedad"] = humedad;
    doc["gas"] = sensorMQ7read;
    
    // Serializamos el JSON a un string
    String jsonString;
    serializeJson(doc, jsonString);
    
    Serial.println("Enviando JSON: " + jsonString);
    
    // Enviamos el JSON como cuerpo de la petición POST
    int httpResponseCode = http.POST(jsonString);

    Serial.print("Código respuesta HTTP: ");
    Serial.println(httpResponseCode);

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("Respuesta del servidor:");
      Serial.println(response);
    } else {
      Serial.print("Error al enviar POST: ");
      Serial.println(http.errorToString(httpResponseCode).c_str());
    }

    http.end();
  } else {
    Serial.println("WiFi no conectado");
  }

  delay(10000);
}