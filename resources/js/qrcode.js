import QRCode from 'qrcode'
import Cropper from 'cropperjs'
import 'cropperjs/dist/cropper.css'

const student = window.student || {}
const user = window.user || {}

const width = 638
const height = 1013
let uploadedImage = null
let cropper = null
let qrData = ''
let imgOffset = { x: 0, y: 0 }
let isDragging = false
let start = { x: 0, y: 0 }

function setupCanvas(canvas, ctx){
    const ratio = window.devicePixelRatio || 1
    canvas.width = width * ratio
    canvas.height = height * ratio
    ctx.setTransform(ratio,0,0,ratio,0,0)
}

function initUpload(){
    const input = document.getElementById('uploadImage')
    const modal = document.getElementById('cropModal')
    const cropImg = document.getElementById('cropImage')
    const confirm = document.getElementById('cropConfirm')
    const cancel = document.getElementById('cropCancel')

    input.addEventListener('change',(e)=>{
        const file = e.target.files[0]
        if(!file) return

        const reader = new FileReader()
        reader.onload = () => {
            cropImg.src = reader.result
            modal.style.display = 'flex'

            cropper?.destroy()
            cropper = new Cropper(cropImg,{
                aspectRatio:1,
                viewMode:1,
                autoCropArea:1
            })
        }
        reader.readAsDataURL(file)
    })

    confirm.addEventListener('click',()=>{
        if(!cropper) return

        const canvas = cropper.getCroppedCanvas({
            width:500,
            height:500
        })

        uploadedImage = canvas.toDataURL('image/png')
        modal.style.display = 'none'
        cropper.destroy()
        cropper = null

        drawFront()
    })

    cancel.addEventListener('click',()=>{
        modal.style.display = 'none'
        cropper?.destroy()
        cropper = null
    })
}

async function generateQR(){
    try{
        const qr_code = user?.qr_code ?? ''
        const name = user?.name ?? ''
        const data = (qr_code && name) ? `${qr_code}@${name}` : 'NO-DATA'

        return await QRCode.toDataURL(data,{
            margin: 1,
            scale: 10,
            errorCorrectionLevel: 'H'
        })
    }catch{
        return ''
    }
}

function loadImage(src){
    return new Promise((resolve,reject)=>{
        const img = new Image()
        img.crossOrigin = 'anonymous'
        img.src = src
        img.onload = ()=>resolve(img)
        img.onerror = reject
    })
}

async function drawFront(){
    const canvas = document.getElementById('frontCanvas')
    const ctx = canvas.getContext('2d')

    setupCanvas(canvas, ctx)

    try{
        const bg = await loadImage('/images/ID.jpg')
        ctx.drawImage(bg,0,0,width,height)
    }catch{
        ctx.fillStyle='#fff'
        ctx.fillRect(0,0,width,height)
    }

    try{
        const qr = await loadImage(qrData)
        ctx.drawImage(qr,width-300,260,270,270)
    }catch{}

    try{
        const img = await loadImage(
            uploadedImage || (user.filepath ? '/storage/'+user.filepath : '/images/avatar.png')
        )

        const size = 280
        const aspect = img.width / img.height

        let sx=0, sy=0, sw=img.width, sh=img.height

        if(aspect > 1){
            sw = img.height
            sx = (img.width - sw)/2 + imgOffset.x
        }else{
            sh = img.width
            sy = (img.height - sh)/2 + imgOffset.y
        }

        sx = Math.max(0, Math.min(sx, img.width - sw))
        sy = Math.max(0, Math.min(sy, img.height - sh))

        const imgX = (width - size)/12
        ctx.drawImage(img,sx,sy,sw,sh,imgX,250,size,size)
    }catch{}

    ctx.fillStyle='#000'
    ctx.textAlign='center'

    const fullName = `${student.FirstName ?? ''} ${student.MiddleName ?? ''} ${student.LastName ?? ''}`.toUpperCase()

    ctx.font='italic 18px Arial'
    ctx.fillText('Student Signature',width/2,870)

    ctx.font='bold 30px Arial'
    ctx.fillText(fullName,width/2,height-340)

    ctx.fillText(student.LRN ?? '',width/3.7,height-445)

    ctx.fillText(user.qr_code ?? '',width/1.35,height-445)

    ctx.font='25px Arial'
    ctx.fillText(`Contact: ${student.PhoneNumber ?? ''}`,width/2,height-290)

    ctx.beginPath()
    ctx.moveTo(width*0.2,850)
    ctx.lineTo(width*0.8,850)
    ctx.stroke()

    ctx.font='bolder 35px Arial'
    ctx.fillText(`G-${student.YearLevel ?? ''} ${student.Strand ?? ''} STUDENT`,width/2,height-48)
}

function drawAcademicTable(ctx,x,y){
    const tableWidth = 618
    const colWidths = [tableWidth*0.3, tableWidth*0.3, tableWidth*0.4]
    const rowHeight = 40

    const baseYear = new Date().getFullYear() + 3

    ctx.font='bold 17px Arial'
    ctx.textAlign='center'

    const headers=['School Year','Grade Level','Class Adviser']
    let colX = x

    headers.forEach((h,i)=>{
        ctx.strokeRect(colX,y,colWidths[i],rowHeight)
        ctx.fillText(h,colX+colWidths[i]/2,y+20)
        colX += colWidths[i]
    })

    for(let i=0;i<6;i++){
        const rowY = y + rowHeight*(i+1)
        const start = baseYear - i

        const values=[`${start}-${start+1}`,'','']

        let cx = x
        values.forEach((v,i)=>{
            ctx.strokeRect(cx,rowY,colWidths[i],rowHeight)
            ctx.fillText(v,cx+colWidths[i]/2,rowY+20)
            cx += colWidths[i]
        })
    }
}

async function drawBack(){
    const canvas = document.getElementById('backCanvas')
    const ctx = canvas.getContext('2d')

    setupCanvas(canvas, ctx)

    try{
        const bg = await loadImage('/images/back.jpg')
        ctx.drawImage(bg,0,0,width,height)
    }catch{}

    ctx.textAlign='center'
    ctx.fillStyle='#000'

    ctx.font='bold 25px Arial'
    ctx.fillText('The bearer of this ID card is a bonafide student of the school.', width/2, 50)
    ctx.fillText('Please wear this at all times inside the campus.', width/2, 80)

    drawAcademicTable(ctx,10,100)

    ctx.fillText('IN CASE OF EMERGENCY, PLEASE CONTACT',width/2,420)
    ctx.fillText('THE PARENT OR GUARDIAN',width/2,445)

    const g = student.guardian ?? {}
    const guardianName = `${g.FirstName ?? ''} ${g.MiddleName ?? ''} ${g.LastName ?? ''}`

    ctx.font='bold 30px Arial'
    ctx.fillText(guardianName,width/2,500)

    ctx.font='bold 20px Arial'
    ctx.fillText(g.Address ?? '',width/2,530)
    ctx.fillText(g.PhoneNumber ?? '',width/2,560)

    ctx.beginPath()
    ctx.moveTo(width*0.2,750)
    ctx.lineTo(width*0.8,750)
    ctx.stroke()

    ctx.font='italic 24px Arial'
    ctx.fillText("Principal's Signature",width/2,780)
}

function initDrag(){
    const canvas = document.getElementById('frontCanvas')

    canvas.addEventListener('mousedown',(e)=>{
        isDragging = true
        start.x = e.offsetX
        start.y = e.offsetY
    })

    canvas.addEventListener('mousemove',(e)=>{
        if(!isDragging) return

        const dx = e.offsetX - start.x
        const dy = e.offsetY - start.y

        imgOffset.x += dx
        imgOffset.y += dy

        start.x = e.offsetX
        start.y = e.offsetY

        drawFront()
    })

    canvas.addEventListener('mouseup',()=>isDragging=false)
    canvas.addEventListener('mouseleave',()=>isDragging=false)
}

async function init(){
    qrData = await generateQR()
    await drawFront()
    await drawBack()
    initDrag()
    initUpload()
}

window.addEventListener('load', init)
