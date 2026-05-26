import sys
import serial
import time

BAUD_RATE = 19200
DEBUG = True


def log(msg):

    if DEBUG:
        print(f"[DEBUG] {msg}")


def send_sms(port, number, message):

    ser = None

    try:

        ser = serial.Serial(
            port,
            BAUD_RATE,
            timeout=5
        )

        time.sleep(3)

        ser.reset_input_buffer()
        ser.reset_output_buffer()

        def send_command(cmd, wait=1):

            log(f"Sending: {cmd}")

            ser.write(
                cmd.encode() + b'\r'
            )

            time.sleep(wait)

            response = ser.read_all().decode(
                errors='ignore'
            )

            log(f"Response: {response}")

            return response

        at_response = send_command("AT")

        if "OK" not in at_response:

            time.sleep(2)

            at_response = send_command("AT")

            if "OK" not in at_response:

                raise Exception(
                    "Modem not responding. "
                    "Possible causes: modem freeze, "
                    "USB disconnected, COM port locked, "
                    "or weak modem power."
                )

        signal = 0

        for attempt in range(1, 6):

            signal_response = send_command(
                "AT+CSQ"
            )

            if "+CSQ:" in signal_response:

                try:

                    signal_raw = signal_response.split(
                        "+CSQ:"
                    )[1].split(",")[0].strip()

                    signal = int(signal_raw)

                    log(
                        f"Signal attempt "
                        f"{attempt}: {signal}"
                    )

                    if signal >= 8:

                        break

                except ValueError:
                    pass

            if attempt < 5:

                log(
                    "Weak/no signal detected. "
                    "Retrying in 5 seconds..."
                )

                time.sleep(5)

        if signal == 99:

            raise Exception(
                "No network signal detected."
            )

        if signal < 8:

            raise Exception(
                f"Weak signal detected ({signal}) "
                f"after multiple retries."
            )

        network_response = send_command(
            "AT+CREG?"
        )

        if (
            ",1" not in network_response
            and
            ",5" not in network_response
        ):

            raise Exception(
                "SIM not registered to GSM network."
            )

        sim_response = send_command(
            "AT+CPIN?"
        )

        if "READY" not in sim_response:

            raise Exception(
                "SIM card is locked or not ready."
            )

        smsc_response = send_command(
            "AT+CSCA?"
        )

        if "OK" not in smsc_response:

            raise Exception(
                "SMS Center Number (SMSC) invalid or unavailable."
            )

        text_mode_response = send_command(
            "AT+CMGF=1"
        )

        if "OK" not in text_mode_response:

            raise Exception(
                "Failed to enable text mode."
            )

        cmgs_response = send_command(
            f'AT+CMGS="{number}"',
            2
        )

        if ">" not in cmgs_response:

            raise Exception(
                "Modem rejected recipient number."
            )

        time.sleep(1)

        log(f"Sending SMS to {number}")

        ser.write(
            message.encode(errors='ignore')
            + b"\x1A"
        )

        time.sleep(8)

        final_response = ser.read_all().decode(
            errors='ignore'
        )

        log(
            f"Final modem response: "
            f"{final_response}"
        )

        upper_response = final_response.upper()

        if "+CMGS:" in upper_response:

            print(
                "SMS sent successfully!"
            )

            return

        if "CMS ERROR" in upper_response:

            if "302" in upper_response:

                raise Exception(
                    "Operation not allowed."
                )

            if "330" in upper_response:

                raise Exception(
                    "SIM card not inserted."
                )

            if "500" in upper_response:

                raise Exception(
                    "Modem internal failure."
                )

            raise Exception(
                f"GSM CMS ERROR: "
                f"{final_response.strip()}"
            )

        if "ERROR" in upper_response:

            raise Exception(
                "SMS sending failed. "
                "Possible causes: no load, weak signal, "
                "network rejection, unsupported message content, "
                "or modem instability."
            )

        raise Exception(
            f"Unexpected modem response: "
            f"{final_response.strip()}"
        )

    except serial.SerialException as e:

        raise Exception(
            f"Serial port error: {str(e)}"
        )

    except Exception as e:

        raise Exception(str(e))

    finally:

        if ser:

            try:

                if ser.is_open:

                    log("Closing serial port...")

                    ser.close()

                    time.sleep(2)

            except Exception:
                pass


if __name__ == "__main__":

    try:

        if len(sys.argv) < 4:

            print(
                "Usage: python send_sms.py "
                "<number> <message> [port]",
                file=sys.stderr
            )

            sys.exit(1)

        number = sys.argv[1]
        message = sys.argv[2]
        port = sys.argv[3]

        log(
            "Running process simulation..."
        )

        send_sms(
            port,
            number,
            message
        )

    except Exception as e:

        print(
            f"Error: {str(e)}",
            file=sys.stderr
        )

        sys.exit(1)
