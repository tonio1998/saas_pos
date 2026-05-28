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

    log(
        f"Sending: {command}"
    )

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

    response = response.strip()

    log(
        f"Response: {response}"
    )

    return response


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

                log(
                    f"Signal: {signal}"
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

    for attempt in range(
        MAX_NETWORK_ATTEMPTS
    ):

        response = send_command(
            ser,
            "AT+CREG?",
            1
        )

        last_response = response

        log(
            f"CREG attempt "
            f"{attempt + 1}: "
            f"{response}"
        )

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
        f"Last response: "
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

            if "CMS ERROR" in upper:

                raise Exception(
                    final_response.strip()
                )

            if "ERROR" in upper:

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


def send_sms(
    port,
    number,
    message
):

    ser = None

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

        messages = split_message(
            message
        )

        total_parts = len(messages)

        for index, part in enumerate(
            messages,
            start=1
        ):

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
                    f"Failed sending "
                    f"part "
                    f"{index}/"
                    f"{total_parts}"
                )

            time.sleep(0.5)

        print(
            "SMS sent successfully!"
        )

        return True

    except serial.SerialException as e:

        raise Exception(
            f"Serial error: {str(e)}"
        )

    except Exception as e:

        raise Exception(
            str(e)
        )

    finally:

        if ser:

            try:

                if ser.is_open:
                    ser.close()

            except Exception:
                pass


if __name__ == "__main__":

    try:

        if len(sys.argv) < 4:

            print(
                "Usage: python "
                "send_sms.py "
                "<number> "
                "<message> "
                "<port>",
                file=sys.stderr
            )

            sys.exit(1)

        number = sys.argv[1]
        message = sys.argv[2]
        port = sys.argv[3]

        send_sms(
            port,
            number,
            message
        )

    except Exception as e:

        print(
            str(e),
            file=sys.stderr
        )

        sys.exit(1)
