from flask import Flask, request, jsonify
import serial
import time

app = Flask(__name__)

@app.route('/send-sms', methods=['POST'])
def send_sms():
    number = request.json.get('number')
    message = request.json.get('message')

    try:
        # Connect to the modem
        ser = serial.Serial('COM4', 9600, timeout=5)
        time.sleep(2)  # Wait for modem to initialize

        # Send AT commands to modem
        ser.write(b'AT\r')
        time.sleep(1)
        ser.write(b'AT+CMGF=1\r')  # Set SMS to text mode
        time.sleep(1)
        ser.write(f'AT+CMGS="{number}"\r'.encode())
        time.sleep(1)
        ser.write(f'{message}\x1A'.encode())  # \x1A = CTRL+Z to send
        time.sleep(3)  # Wait for message to send

        ser.close()

        return jsonify({"status": "success", "message": "SMS sent."})

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

if __name__ == '__main__':
    app.run(port=5000)
