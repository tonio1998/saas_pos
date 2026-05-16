import sys
import serial
import time
import serial.tools.list_ports

# === CONFIG ===
BAUD_RATE = 19200
DEBUG = True  # Set to False to disable debug output


def log(msg):
    if DEBUG:
        print(f"[DEBUG] {msg}")


def find_serial_port():
    try:
        ports = list(serial.tools.list_ports.comports())
    except Exception as e:
        print(f"❌ Failed to list COM ports: {e}", file=sys.stderr)
        sys.exit(1)

    if not ports:
        print("❌ No serial ports found.", file=sys.stderr)
        sys.exit(1)

    for port in ports:
        desc = getattr(port, 'description', '')
        if "CH340" in desc or "USB-SERIAL" in desc:
            print(f"✅ Using port: {port.device} - {desc}")
            return port.device

    print("❌ CH340/USB-SERIAL device not found.", file=sys.stderr)
    print("➡️ Available ports:")
    for port in ports:
        print(f" - {port.device}: {getattr(port, 'description', 'No description')}")
    sys.exit(1)


def run_process_simulation():
    """
    Simulate or run your processing logic here.
    Replace this with actual checks or processing.
    """
    log("Running process simulation...")
    # Simulate success or failure
    return True  # Change to False to simulate failure


def send_sms(port, number, message):
    ser = None
    try:
        ser = serial.Serial(port, BAUD_RATE, timeout=5)
        time.sleep(2)  # Wait for modem to initialize

        def send_command(cmd, wait=1):
            log(f"Sending: {cmd}")
            ser.write(cmd.encode() + b'\r')
            time.sleep(wait)
            response = ser.read_all().decode(errors='ignore')
            log(f"Response: {response}")
            return response

        if "OK" not in send_command("AT"):
            raise Exception("Modem not responding to 'AT'")

        if "OK" not in send_command("AT+CMGF=1"):
            raise Exception("Failed to set text mode")

        response = send_command(f'AT+CMGS="{number}"')
        if ">" not in response:
            raise Exception("Modem did not accept message command")

        time.sleep(1)
        ser.write(message.encode() + b"\x1A")
        time.sleep(5)

        final_response = ser.read_all().decode(errors='ignore')
        log(f"Final modem response: {final_response}")

        if "+CMGS:" in final_response:
            print("SMS sent successfully!")
        elif "ERROR" in final_response:
            raise Exception(f"Modem error: {final_response.strip()}")
        else:
            raise Exception("Unexpected response from modem")

    except serial.SerialException as e:
        raise e  # Let the wrapper handle retry
    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
        sys.exit(1)
    finally:
        if ser and ser.is_open:
            log("Closing serial port...")
            ser.close()


def safe_send_sms(port, number, message, retries=3):
    for attempt in range(1, retries + 1):
        try:
            send_sms(port, number, message)
            return  # Success
        except serial.SerialException as e:
            print(f"Attempt {attempt}: Serial error - {e}")
            if attempt < retries:
                time.sleep(2)
                continue
            else:
                print("Failed after multiple attempts.")
                sys.exit(1)


if __name__ == "__main__":
    try:
        if len(sys.argv) < 3:
            print("Usage: python send_sms.py <number> <message> [port]", file=sys.stderr)
            sys.exit(1)

        number = sys.argv[1]
        message = sys.argv[2]
        port = sys.argv[3] if len(sys.argv) >= 4 else find_serial_port()

        if not run_process_simulation():
            print("Process failed. SMS will not be sent.", file=sys.stderr)
            sys.exit(1)

        safe_send_sms(port, number, message)
    except Exception as e:
        print(f"Fatal error: {e}", file=sys.stderr)
        sys.exit(1)
