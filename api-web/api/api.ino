#include <WiFi.h>        // Incluimos WiFi libreria.
#include <HTTPClient.h>  // Incluimos HTTPClient libreria.
#include <DHT11.h>       // Incluimos DHT11 libreria para el sensor.

// Establecemos los datos de rec WiFi
const char* ssid = "Red A52s";
const char* password = "12345678";

// Definimos URL de la API a la cual enviaremos los datos
const char* serverName = "https://ucv.vcodesystems.com/api/post.php";

DHT11 dht11(18);  // Crea una instancia DHT11 y enviamos el PIN conectado a la placa EPS32

//Definimos el PIN de los LED para alerta
int ledVerde = 26;
int ledRojo = 27;

//Definimos el PIN de entrada para el sensonr
int sensorMQ7 = 18;

void setup() {
  // Definimos la velocidad de la comunicació serial
  Serial.begin(115200);

  //Definimos los pines como salida
  pinMode(ledVerde, OUTPUT);
  pinMode(ledRojo, OUTPUT);

  // Establecemos conexión WiFi
  WiFi.begin(ssid, password);
  Serial.print("Conectando a WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi conectado");
}

void loop() {
  // Creamos las varianles para guardar los datos obtenidos por el sensor
  int temperatura = 0;
  int humedad = 0;

  //Creamos una variable para leer el pin del sensor
  int sensorMQ7read = analogRead(sensorMQ7);

  // Intentar leer los valores de temperatura y humedad del sensor DHT11
  int resultado = dht11.readTemperatureHumidity(temperatura, humedad);

  // Verificamos el resultado de las lectura, si la lectura es exitosa, enviamos valores de temperatura y humedad a la API.
  // Si hay errores, imprimimos el mensaje de error.
  if (resultado == 0) {
    Serial.print("Temperature: ");
    Serial.print(temperatura);
    Serial.print(" °C\tHumidity: ");
    Serial.print(humedad);
    Serial.println(" %");    
  } else {
    // Print error message based on the error code.
    Serial.println(DHT11::getErrorString(resultado));
  }

  Serial.print("Gas Sensor: ");
  Serial.print(sensorMQ7read); /*Read value printed*/
  Serial.print("\t");
  Serial.print("\t");

  if (sensorMQ7read > 1800) { /*if condition with threshold 1800*/
    Serial.println("Gas");
    digitalWrite(ledRojo, HIGH); /*LED set HIGH if Gas detected */
  } else {
    Serial.println("No Gas");
    digitalWrite(ledVerde, HIGH); /*LED set LOW if NO Gas detected */
  }

      // Enviar datos
    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http;
      http.begin(serverName);
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");

      // Datos en formato POST
      String httpRequestData = "temperatura=" + String(temperatura) + "&humedad=" + String(humedad) + "&gas=" + String(sensorMQ7read);

      int httpResponseCode = http.POST(httpRequestData);

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
