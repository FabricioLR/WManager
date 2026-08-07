#!/bin/bash

curl -X POST "http://127.0.0.1:8000/api/messages/send" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-Api-Secret: 123456" \
  -d '{
    "phone_number": "556195798701",
    "message": "Hello from curl test!"
  }'