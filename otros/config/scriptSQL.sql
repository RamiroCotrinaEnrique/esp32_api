CREATE DATABASE iot;

CREATE TABLE sensorDTH11 (
    dth11_id int(11) AUTO_INCREMENT, 
    dth11_temperatura decimal(5,2) DEFAULT NULL,
    dth11_humedad decimal(5,2) DEFAULT NULL, 
    dth11_fecha_create timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    dth11_fecha_update datetime,
    dth11_fecha_delete datetime,
    PRIMARY KEY (dth11_id)
);

CREATE TABLE sensorMQ7 (
    mq7_id int(11) AUTO_INCREMENT, 
    mq7_gas decimal(5,2) DEFAULT NULL, 
    mq7_fecha_create timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    mq7_fecha_update datetime,
    mq7_fecha_delete datetime,
    PRIMARY KEY (mq7_id)
) ;