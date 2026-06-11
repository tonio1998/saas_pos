import config from '../config';

const POSHelpers = {

    currency(value) {

        return new Intl.NumberFormat(
            config.locale,
            {
                style: 'currency',
                currency: config.currency
            }
        ).format(value);
    },

    debounce(
        callback,
        delay = 300
    ) {

        let timer;

        return (...args) => {

            clearTimeout(
                timer
            );

            timer =
                setTimeout(
                    () =>
                        callback(
                            ...args
                        ),
                    delay
                );
        };
    }

};

export default POSHelpers;
