<script>

    document.addEventListener(
        'DOMContentLoaded',
        () => {

            const $ = id =>
                document.getElementById(id);

            const input =
                $('scan-input');

            const statusBar =
                $('status-bar');

            const personName =
                $('person-name');

            const personRole =
                $('person-role');

            const personPhoto =
                $('person-photo');

            const scanTime =
                $('scan-time');

            const scanCount =
                $('scan-count');

            const greetingText =
                $('greeting-text');

            const liveClock =
                $('live-clock');

            const successAudio =
                $('success-sound');

            const errorAudio =
                $('error-sound');

            let processing = false;

            let resetTimeout = null;

            initialize();

            function initialize(){

                renderLogs();

                startClock();

                resetScanner();

                bindEvents();

                focusInput();

            }

            function bindEvents(){

                document.addEventListener(
                    'click',
                    focusInput
                );

                document.addEventListener(
                    'keydown',
                    focusInput
                );

                input?.addEventListener(
                    'keydown',
                    handleScanInput
                );

                window.addEventListener(
                    'focus',
                    focusInput
                );

            }

            function focusInput(){

                if(!input){
                    return;
                }

                input.focus();

            }

            function handleScanInput(e){

                if(e.key !== 'Enter'){
                    return;
                }

                e.preventDefault();

                if(processing){
                    return;
                }

                const code =
                    input.value.trim();

                input.value = '';

                if(!code){
                    return;
                }

                processing = true;

                processScan(code);

            }

            function startClock(){

                if(!liveClock){
                    return;
                }

                const updateClock = () => {

                    liveClock.innerText =
                        new Date()
                            .toLocaleTimeString(
                                [],
                                {
                                    hour:'2-digit',
                                    minute:'2-digit',
                                    second:'2-digit'
                                }
                            );

                };

                updateClock();

                setInterval(
                    updateClock,
                    1000
                );

            }

            async function processScan(code){

                try{

                    const response =
                        await fetch(
                            '/scan',
                            {
                                method:'POST',

                                headers:{
                                    'Content-Type':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                    document.querySelector(
                                        'meta[name="csrf-token"]'
                                    )?.content
                                },

                                body:JSON.stringify({
                                    code
                                })
                            }
                        );

                    const data =
                        await response.json();

                    if(!response.ok){
                        throw data;
                    }

                    handleSuccess(data);

                    addLog(data);

                }catch(error){

                    console.error(error);

                    handleError();

                }finally{

                    processing = false;

                    focusInput();

                }

            }

            function handleSuccess(data){

                clearResetTimeout();

                const fullName =
                    data.name || 'UNKNOWN';

                const mode =
                    (
                        data.mode ||
                        data.type ||
                        data.action ||
                        ''
                    )
                        .toUpperCase();

                const isTimeIn =
                    mode === 'TIME_IN';

                personName.innerText =
                    fullName;

                personRole.innerText =
                    data.role ||
                    'AUTHORIZED PERSONNEL';

                personPhoto.src =
                    data.photo ||
                    '/images/avatar.png';

                scanTime.innerText =
                    data.time ||
                    getCurrentTime();

                greetingText.innerText =
                    isTimeIn
                        ? '👋 WELCOME'
                        : '🚪 GOODBYE';

                greetingText.style.color =
                    isTimeIn
                        ? '#fde047'
                        : '#bfdbfe';

                updateStatusBar({

                    className:
                        isTimeIn
                            ? 'success'
                            : 'out',

                    title:
                        isTimeIn
                            ? 'ENTRY RECORDED'
                            : 'EXIT RECORDED',

                    subtitle:
                        data.message ||
                        (
                            isTimeIn
                                ? 'Access granted'
                                : 'Exit recorded'
                        )

                });

                playAudio(
                    successAudio
                );

                flash(
                    'success-flash'
                );

                speakMessage(
                    isTimeIn
                        ? `Welcome ${fullName}`
                        : `Goodbye ${fullName}`
                );

                queueReset();

            }

            function handleError(){

                clearResetTimeout();

                personName.innerText =
                    'ACCESS DENIED';

                personRole.innerText =
                    'INVALID QR OR RFID';

                personPhoto.src =
                    '/images/avatar.png';

                greetingText.innerText =
                    '⚠ ACCESS DENIED';

                greetingText.style.color =
                    '#fecaca';

                scanTime.innerText =
                    '--:--';

                updateStatusBar({

                    className:'error',

                    title:'ACCESS DENIED',

                    subtitle:'Invalid scan detected'

                });

                playAudio(
                    errorAudio
                );

                flash(
                    'error-flash'
                );

                speakMessage(
                    'Access denied'
                );

                queueReset();

            }

            function updateStatusBar({
                                         className,
                                         title,
                                         subtitle
                                     }){

                if(!statusBar){
                    return;
                }

                statusBar.className =
                    `status-bar ${className}`;

                const titleEl =
                    statusBar.querySelector(
                        '.status-title'
                    );

                const subtitleEl =
                    statusBar.querySelector(
                        '.status-subtitle'
                    );

                if(titleEl){
                    titleEl.innerText =
                        title;
                }

                if(subtitleEl){
                    subtitleEl.innerText =
                        subtitle;
                }

            }

            function resetScanner(){

                personName.innerText =
                    'WAITING...';

                personRole.innerText =
                    'TAP RFID CARD OR SCAN QR';

                personPhoto.src =
                    '/images/avatar.png';

                scanTime.innerText =
                    '--:--';

                greetingText.innerText =
                    'READY TO SCAN';

                greetingText.style.color =
                    '#fde047';

                updateStatusBar({

                    className:'idle',

                    title:'SCANNER READY',

                    subtitle:'Waiting for scan'

                });

                focusInput();

            }

            function queueReset(){

                resetTimeout =
                    setTimeout(
                        resetScanner,
                        1800
                    );

            }

            function clearResetTimeout(){

                if(resetTimeout){

                    clearTimeout(
                        resetTimeout
                    );

                }

            }

            function playAudio(audio){

                if(!audio){
                    return;
                }

                audio.currentTime = 0;

                audio.play()
                    .catch(() => {});

            }

            function flash(className){

                document.body.classList.add(
                    className
                );

                setTimeout(() => {

                    document.body.classList.remove(
                        className
                    );

                },450);

            }

            function speakMessage(message){

                try{

                    if(
                        !(
                            'speechSynthesis'
                            in
                            window
                        )
                    ){
                        return;
                    }

                    window
                        .speechSynthesis
                        .cancel();

                    const speech =
                        new SpeechSynthesisUtterance(
                            message
                        );

                    const voices =
                        window
                            .speechSynthesis
                            .getVoices();

                    speech.voice =
                        voices.find(v =>
                            v.lang === 'fil-PH'
                        )
                        ||
                        voices.find(v =>
                            v.lang === 'en-US'
                        )
                        ||
                        null;

                    speech.lang = 'fil-PH';

                    speech.rate = 0.92;

                    speech.pitch = 0.96;

                    speech.volume = 1;

                    window
                        .speechSynthesis
                        .speak(speech);

                }catch(error){

                    console.error(
                        'Speech synthesis error:',
                        error
                    );

                }

            }

            function getCurrentTime(){

                return new Date()
                    .toLocaleTimeString(
                        [],
                        {
                            hour:'2-digit',
                            minute:'2-digit'
                        }
                    );

            }

            function getTodayKey(){

                return (
                    'scan_logs_' +
                    new Date()
                        .toISOString()
                        .slice(0,10)
                );

            }

            function addLog(data){

                const key =
                    getTodayKey();

                const logs =
                    JSON.parse(
                        localStorage.getItem(key)
                        || '[]'
                    );

                logs.unshift(data);

                if(logs.length > 50){

                    logs.pop();

                }

                localStorage.setItem(
                    key,
                    JSON.stringify(logs)
                );

                scanCount.innerText =
                    logs.length;

            }

            function renderLogs(){

                const logs =
                    JSON.parse(
                        localStorage.getItem(
                            getTodayKey()
                        ) || '[]'
                    );

                scanCount.innerText =
                    logs.length;

            }

        });

</script>
