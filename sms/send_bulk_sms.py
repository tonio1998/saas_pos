
import json
import math
import sys
import serial
import time

BAUD_RATE = 19200
TIMEOUT = 3

DEBUG = False

MIN_SIGNAL = 8

MAX_SIGNAL_ATTEMPTS = 5
MAX_NETWORK_ATTEMPTS = 10
MAX_SEND_RETRY = 2

SIGNAL_DELAY = 1
NETWORK_DELAY = 2

MODEM_BOOT_DELAY = 2

SMS_SEND_TIMEOUT = 10

MAX_SINGLE_SMS = 160
MAX_MULTI_SMS = 153

SEND_DELAY = 0.3


def log(message):

    if DEBUG:
        print(f"[DEBUG] {message}")


def split_message(message):

    if len(message) <= MAX_SINGLE_SMS:
        return [message]

    parts = []

    total_parts = math.ceil(
        len(message) / MAX_MULTI_SMS
    )

    for i in range(total_parts):

        start = i * MAX_MULTI_SMS
        end = start + MAX_MULTI_SMS

        part = message[start:end]

        prefix = (
            f"({i + 1}/{total_parts}) "
        )

        parts.append(
            prefix + part
        )

    return parts


def send_command(
    ser,
    command,
    wait=0.5
):

    ser.reset_input_buffer()

    ser.write(
        (command + "\r").encode()
    )

    time.sleep(wait)

    response = ""

    start_time = time.time()

    while True:

        if ser.in_waiting:

            response += ser.read(
                ser.in_waiting
            ).decode(
                errors="ignore"
            )

        if (
            "OK" in response
            or
            "ERROR" in response
            or
            ">" in response
        ):

            break

        if (
            time.time()
            - start_time
        ) >= 2:

            break

        time.sleep(0.1)

    return response.strip()


def wait_for_signal(ser):

    signal = 0

    for _ in range(
        MAX_SIGNAL_ATTEMPTS
    ):

        response = send_command(
            ser,
            "AT+CSQ",
            0.5
        )

        if "+CSQ:" in response:

            try:

                signal = int(
                    response
                    .split("+CSQ:")[1]
                    .split(",")[0]
                    .strip()
                )

                if signal >= MIN_SIGNAL:
                    return

            except Exception:
                pass

        time.sleep(
            SIGNAL_DELAY
        )

    raise Exception(
        f"Weak signal ({signal})"
    )


def wait_for_network(ser):

    last_response = ""

    for _ in range(
        MAX_NETWORK_ATTEMPTS
    ):

        response = send_command(
            ser,
            "AT+CREG?",
            1
        )

        last_response = response

        if (
            ",1" in response
            or
            ",5" in response
        ):

            return

        if ",3" in response:

            raise Exception(
                "Network registration denied."
            )

        time.sleep(
            NETWORK_DELAY
        )

    raise Exception(
        f"Network registration failed. "
        f"{last_response}"
    )


def initialize_modem(ser):

    response = send_command(
        ser,
        "AT",
        0.5
    )

    if "OK" not in response:

        raise Exception(
            "Modem not responding."
        )

    send_command(
        ser,
        "AT+CMEE=1",
        0.3
    )

    send_command(
        ser,
        "AT+CMGF=1",
        0.3
    )

    wait_for_signal(ser)

    wait_for_network(ser)


def send_single_sms(
    ser,
    number,
    message
):

    response = send_command(
        ser,
        f'AT+CMGS="{number}"',
        1
    )

    if ">" not in response:

        raise Exception(
            "Recipient rejected."
        )

    ser.write(
        message.encode(
            errors="ignore"
        ) + b"\x1A"
    )

    final_response = ""

    start_time = time.time()

    while True:

        if ser.in_waiting:

            chunk = ser.read(
                ser.in_waiting
            ).decode(
                errors="ignore"
            )

            final_response += chunk

            upper = (
                final_response.upper()
            )

            if "+CMGS:" in upper:
                return True

            if (
                "CMS ERROR" in upper
                or
                "ERROR" in upper
            ):

                raise Exception(
                    final_response.strip()
                )

        if (
            time.time()
            - start_time
        ) >= SMS_SEND_TIMEOUT:

            raise Exception(
                "SMS timeout."
            )

        time.sleep(0.2)


def send_bulk_sms(
    port,
    messages
):

    ser = None

    results = []

    try:

        ser = serial.Serial(
            port=port,
            baudrate=BAUD_RATE,
            timeout=TIMEOUT
        )

        time.sleep(
            MODEM_BOOT_DELAY
        )

        initialize_modem(ser)

        for sms in messages:

            sms_id = sms["id"]

            number = sms["phone"]

            message = sms["message"]

            try:

                parts = split_message(
                    message
                )

                for part in parts:

                    success = False

                    for _ in range(
                        MAX_SEND_RETRY
                    ):

                        try:

                            send_single_sms(
                                ser,
                                number,
                                part
                            )

                            success = True

                            break

                        except Exception:

                            time.sleep(1)

                    if not success:

                        raise Exception(
                            "SMS send failed."
                        )

                    time.sleep(
                        SEND_DELAY
                    )

                results.append({

                    "id": sms_id,

                    "success": True,

                    "error": None,
                })

            except Exception as e:

                results.append({

                    "id": sms_id,

                    "success": False,

                    "error": str(e),
                })

        print(
            json.dumps(results)
        )

        return True

    finally:

        if ser:

            try:

                if ser.is_open:
                    ser.close()

            except Exception:
                pass


if __name__ == "__main__":

    try:

        if len(sys.argv) < 3:

            print(
                "Usage: python "
                "send_bulk_sms.py "
                "<json_payload> "
                "<port>",
                file=sys.stderr
            )

            sys.exit(1)

        messages = json.loads(
            sys.argv[1]
        )

        port = sys.argv[2]

        send_bulk_sms(
            port,
            messages
        )

    except Exception as e:

        print(
            str(e),
            file=sys.stderr
        )

        sys.exit(1)
