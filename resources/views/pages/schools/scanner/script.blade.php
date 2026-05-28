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

            const csrfToken =
                document.querySelector(
                    'meta[name="csrf-token"]'
                )?.content;

            let processing = false;

            let resetTimeout = null;

            let activeController = null;

            let lastScan = '';

            let lastScanTime = 0;

            initialize();

            function initialize(){

                renderLogs();

                startClock();

                resetScanner();

                bindEvents();

                preloadAssets();

                requestAnimationFrame(
                    focusInput
                );

            }

            function preloadAssets(){

                const img =
                    new Image();

                img.src =
                    '/images/avatar.png';

            }

            function bindEvents(){

                document.addEventListener(
                    'click',
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

                if(
                    !input
                    ||
                    document.activeElement
                    === input
                ){
                    return;
                }

                input.focus();

            }

            function handleScanInput(e){

                if(
                    e.key !== 'Enter'
                ){
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

                const now =
                    Date.now();

                if(

                    lastScan === code

                    &&

                    now - lastScanTime
                    < 1200

                ){

                    return;

                }

                lastScan = code;

                lastScanTime = now;

                processing = true;

                showProcessingState(
                    code
                );

                processScan(code);

            }

            function startClock(){

                if(!liveClock){
                    return;
                }

                const updateClock = () => {

                    liveClock.textContent =
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

            function showProcessingState(code){

                clearResetTimeout();

                personName.textContent =
                    'PROCESSING...';

                personRole.textContent =
                    code;

                greetingText.textContent =
                    '⏳ VERIFYING';

                greetingText.style.color =
                    '#fde047';

                scanTime.textContent =
                    getCurrentTime();

                updateStatusBar({

                    className:'idle',

                    title:'VERIFYING ACCESS',

                    subtitle:'Please wait...'
                });

            }

            async function processScan(code){

                try{

                    if(activeController){

                        activeController
                            .abort();

                    }

                    activeController =
                        new AbortController();

                    const response =
                        await fetch(
                            '/scan',
                            {
                                method:'POST',

                                signal:
                                activeController.signal,

                                headers:{
                                    'Content-Type':
                                        'application/json',

                                    'X-CSRF-TOKEN':
                                    csrfToken
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

                    requestIdleCallback(
                        () => {

                            addLog(data);

                        }
                    );

                }catch(error){

                    if(
                        error.name
                        === 'AbortError'
                    ){

                        return;

                    }

                    console.error(error);

                    handleError();

                }finally{

                    processing = false;

                    requestAnimationFrame(
                        focusInput
                    );

                }

            }

            function handleSuccess(data){

                clearResetTimeout();

                const fullName =
                    data.name
                    || 'UNKNOWN';

                const mode =
                    (
                        data.mode
                        ||
                        data.type
                        ||
                        data.action
                        ||
                        ''
                    )
                        .toUpperCase();

                const isTimeIn =
                    mode === 'TIME_IN';

                personName.textContent =
                    fullName;

                personRole.textContent =
                    data.role
                    || 'AUTHORIZED PERSONNEL';

                if(data.photo){

                    const img =
                        new Image();

                    img.src =
                        data.photo;

                    img.onload = () => {

                        personPhoto.src =
                            data.photo;

                    };

                }else{

                    personPhoto.src =
                        '/images/avatar.png';

                }

                scanTime.textContent =
                    data.time
                    || getCurrentTime();

                greetingText.textContent =
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
                        data.message
                        ||
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

                queueReset();

            }

            function handleError(){

                clearResetTimeout();

                personName.textContent =
                    'ACCESS DENIED';

                personRole.textContent =
                    'INVALID QR OR RFID';

                personPhoto.src =
                    '/images/avatar.png';

                greetingText.textContent =
                    '⚠ ACCESS DENIED';

                greetingText.style.color =
                    '#fecaca';

                scanTime.textContent =
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

                    titleEl.textContent =
                        title;

                }

                if(subtitleEl){

                    subtitleEl.textContent =
                        subtitle;

                }

            }

            function resetScanner(){

                personName.textContent =
                    'WAITING...';

                personRole.textContent =
                    'TAP RFID CARD OR SCAN QR';

                personPhoto.src =
                    '/images/avatar.png';

                scanTime.textContent =
                    '--:--';

                greetingText.textContent =
                    'READY TO SCAN';

                greetingText.style.color =
                    '#fde047';

                updateStatusBar({

                    className:'idle',

                    title:'SCANNER READY',

                    subtitle:'Waiting for scan'

                });

                requestAnimationFrame(
                    focusInput
                );

            }

            function queueReset(){

                resetTimeout =
                    setTimeout(
                        resetScanner,
                        1200
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

                },300);

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

                try{

                    const key =
                        getTodayKey();

                    const logs =
                        JSON.parse(
                            localStorage.getItem(
                                key
                            ) || '[]'
                        );

                    logs.unshift(data);

                    if(
                        logs.length > 50
                    ){

                        logs.pop();

                    }

                    localStorage.setItem(
                        key,
                        JSON.stringify(logs)
                    );

                    scanCount.textContent =
                        logs.length;

                }catch(error){

                    console.error(error);

                }

            }

            function renderLogs(){

                try{

                    const logs =
                        JSON.parse(
                            localStorage.getItem(
                                getTodayKey()
                            ) || '[]'
                        );

                    scanCount.textContent =
                        logs.length;

                }catch(error){

                    scanCount.textContent =
                        '0';

                }

            }

        });

</script>
