### Description
Measurements:
* High Precipitation
* Harmful UV rays - UV index

Requirements:
* Providers - multiple with averages
* Time period - hourly with pauses
* Location - cities
* Threshold values - dynamic

Application collects weather for each subscribed city every hour\
Application sends notifications via
* telegram
* email

### Run application
In order for application to work:
1. add .env
```
WEATHERAPI_API_KEY=
VISUALCROSSING_API_KEY=
TELEGRAM_BOT_TOKEN=
```
2. run docker container
```bash
sail up -d
```
4. run migrations 
```bash
sail migrate:fresh --seed
```
4. compile view 
```bash
sail npm install && sail npm run dev
```

### Run tests
1. create .env.testing. Specify database `testing`
2. run migrations
```bash
sail migrate:fresh --seed --env=testing
```
3. run tests
```bash
sail test
```
