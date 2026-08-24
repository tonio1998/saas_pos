import './bootstrap'
import $ from 'jquery'
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;
window.$ = window.jQuery = $
import select2 from 'select2'
select2($)
import { Html5QrcodeScanner } from "html5-qrcode"

import 'datatables.net-bs5'
import 'datatables.net-buttons-bs5'
import 'datatables.net-buttons/js/buttons.html5'
import 'datatables.net-buttons/js/buttons.print'

import JSZip from 'jszip'
window.JSZip = JSZip

import Swal from 'sweetalert2'
window.Swal = Swal
import './roles-drag.js'
import './permissions-drag.js'
import './custom.js'
import Chart from 'chart.js/auto';
window.Chart = Chart;

import './utils.js'
import './app-confirm.js'



document.addEventListener("DOMContentLoaded",function(){
    document.addEventListener('click', function (e) {

        const btn = e.target.closest('.btn-actions');

        if (!btn) return;

        actionModalTitle.textContent = btn.dataset.title;

        const template = document.getElementById(btn.dataset.template);

        actionModalBody.innerHTML = template.innerHTML;

        bootstrap.Modal.getOrCreateInstance(actionModal).show();

    });

    document.querySelectorAll(".password-toggle").forEach(toggle=> {
        toggle.addEventListener("click",function(){
            const input = this.previousElementSibling
            const type = input.type === "password" ? "text" : "password"
            input.type = type
            this.classList.toggle("bi-eye")
            this.classList.toggle("bi-eye-slash")
        })
    })

    const phone = document.getElementById('PhoneNumber');

    if (!phone) return;

    phone.addEventListener('keyup', function () {

        let value = this.value.trim();

        if (value.startsWith('09')) {
            this.value = '+63' + value.substring(1);
        }

    });
})

$(function(){
    $(document).on('click', '.activate-user', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: 'Activate User?',
            text: 'This user will be able to log in again.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Activate',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) return;

            $.ajax({
                url: `/school-users/${id}/activate`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });

                    $('#your-table-id').DataTable().ajax.reload(null, false);
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to activate user.'
                    });
                }
            });
        });
    });

    window.initAppSelect2 = function(context) {
        const root = context ? $(context) : $(document);
        root.find('.select2').each(function(){
            const $el = $(this);
            const modalParent = $el.closest('.modal');

            // If inside a hidden modal and context is not that modal, wait until shown.bs.modal
            if (modalParent.length && !modalParent.hasClass('show') && !context) {
                return;
            }

            if ($el.hasClass('select2-hidden-accessible')) {
                $el.select2('destroy');
            }

            let ajaxUrl = $el.data('ajax');
            let placeholder = $el.data('placeholder') || 'Select option';
            let customParent = $el.data('dropdown-parent');
            let dropdownParentTarget = customParent ? $(customParent) : (modalParent.length ? modalParent : null);

            let config = {
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: placeholder,
                allowClear: Boolean($el.data('allow-clear') ?? true),
            };

            if (dropdownParentTarget && dropdownParentTarget.length) {
                config.dropdownParent = dropdownParentTarget;
            }

            if (ajaxUrl) {
                config.ajax = {
                    url: ajaxUrl,
                    dataType: 'json',
                    delay: 250,
                    data: function(params){
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data){
                        return {
                            results: data
                        };
                    }
                };
            }

            $el.select2(config);
        });
    };

    window.initAppSelect2();

    // Re-init on Bootstrap modals shown event so select2 calculates correct 100% width
    $(document).on('shown.bs.modal', function(e){
        window.initAppSelect2(e.target);
    });
})

document.addEventListener('DOMContentLoaded', function(){
    const currentUrl = window.location.href;
    document.querySelectorAll('.sidebar-sublink').forEach(link => {
        if(currentUrl.includes(link.getAttribute('href'))){
            link.classList.add('active');
            const collapse = link.closest('.collapse');
            if(collapse){
                collapse.classList.add('show');
            }
            const parentLink = collapse?.previousElementSibling;
            if(parentLink){
                parentLink.classList.remove('collapsed');
                parentLink.classList.add('active');
            }

        }

    });

    $(document).on('click','.btn-password',function(){
        let url = $(this).data('url');
        let type = $(this).data('type');
        let title = type === 'regenerate'
            ? 'Regenerate Password?'
            : 'Generate Password?';
        let text = type === 'regenerate'
            ? 'This will reset the existing user password.'
            : 'This will create a login account and generate a password.';
        Swal.fire({
            icon:'warning',
            title:title,
            text:text,
            showCancelButton:true,
            confirmButtonText:'Yes, proceed',
            cancelButtonText:'Cancel'
        }).then(function(result){
            if(!result.isConfirmed) return;
            console.log(url);
            $.ajax({
                url:url,
                type:'POST',
                headers:{
                    'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
                },
                success:function(res){
                    if(res.success){
                        Swal.fire({
                            icon:'success',
                            title:res.message,
                            html:
                                '<b>Username:</b> '+res.username+
                                '<br><b>Password:</b> '+res.password
                        });
                    }else{
                        Swal.fire({
                            icon:'error',
                            title:'Error',
                            text:res.message
                        });
                    }
                },
                error:function(){
                    Swal.fire({
                        icon:'error',
                        title:'Server Error',
                        text:'Something went wrong.'
                    });
                }
            });
        });
    });
});

document.addEventListener("DOMContentLoaded", () => {
    const qrContainer = document.getElementById("qr-reader")
    if(qrContainer){
        const qrScanner = new Html5QrcodeScanner(
            "qr-reader",
            {
                fps:10,
                qrbox:250
            },
            false
        )
        qrScanner.render(
            (decodedText)=>{
                document.getElementById("qr-result").value = decodedText
            },
            (error)=>{}
        )
    }

    const nfcBtn = document.getElementById("scan-nfc")

    if(nfcBtn){
        nfcBtn.addEventListener("click", async ()=>{
            if(!("NDEFReader" in window)){
                alert("NFC not supported on this device")
                return
            }
            try{
                const ndef = new NDEFReader()
                await ndef.scan()
                ndef.onreading = event => {
                    const decoder = new TextDecoder()
                    for(const record of event.message.records){
                        const text = decoder.decode(record.data)
                        document.getElementById("nfc-result").value = text
                    }
                }
            }catch(err){
                alert("NFC scan failed")
            }
        })
    }
})
