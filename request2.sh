#!/bin/bash
curl -X POST "http://127.0.0.1:8000/api/whatsapp/webhook" \
  -H "Content-Type: application/json" \
  -d '{
  "object": "whatsapp_business_account",
      "entry": [
        {
          "id": "2003257443888225",
          "changes": [
            {
              "value": {
                "messaging_product": "whatsapp",
                "metadata": {
                  "display_phone_number": "556183450012",
                  "phone_number_id": "1204821132713658"
                },
                "contacts": [
                  {
                    "profile": {
                      "name": "Fabrício"
                    },
                    "wa_id": "556195798701",
                    "user_id": "BR.2610227519432898"
                  }
                ],
                "messages": [
                  {
                    "from": "556195798701",
                    "from_user_id": "BR.2610227519432898",
                    "id": "wamid.HBgMNTU2MTk1Nzk4NzAxFQIAEhggQUMxOTUyMjIyREQ2NkQyMzcwMDYyNjdFNzQzODY3RTIA",
                    "timestamp": "1786563646",
                    "text": {
                      "body": "Oi"
                    },
                    "type": "text"
                  }
                ]
              },
              "field": "messages"
            }
          ]
        }
      ]
}'