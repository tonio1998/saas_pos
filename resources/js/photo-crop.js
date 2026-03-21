import $ from 'jquery'
window.$ = window.jQuery = $

const WIDTH = 413
const HEIGHT = 531

const canvas = document.getElementById('photoCanvas')
const ctx = canvas.getContext('2d')

canvas.width = WIDTH
canvas.height = HEIGHT

const upload = document.getElementById('uploadPhoto')
const zoomIn = document.getElementById('zoomIn')
const zoomOut = document.getElementById('zoomOut')
const resetBtn = document.getElementById('resetPhoto')
const saveBtn = document.getElementById('saveBtn')
const hiddenInput = document.getElementById('croppedPhoto')

let img = null
let scale = 1
let offsetX = 0
let offsetY = 0

let dragging = false
let startX = 0
let startY = 0

const clamp = (val, min, max) => Math.max(min, Math.min(max, val))

function getBounds(imgW, imgH){
    const minX = WIDTH - imgW
    const minY = HEIGHT - imgH
    return {
        minX: Math.min(0, minX),
        maxX: 0,
        minY: Math.min(0, minY),
        maxY: 0
    }
}

function draw(){
    ctx.clearRect(0,0,WIDTH,HEIGHT)

    if(!img) return

    const imgW = img.width * scale
    const imgH = img.height * scale

    const bounds = getBounds(imgW, imgH)

    offsetX = clamp(offsetX, bounds.minX, bounds.maxX)
    offsetY = clamp(offsetY, bounds.minY, bounds.maxY)

    ctx.drawImage(img, offsetX, offsetY, imgW, imgH)

    ctx.strokeStyle = 'rgba(0,0,0,0.2)'
    ctx.lineWidth = 2
    ctx.strokeRect(0,0,WIDTH,HEIGHT)

    ctx.beginPath()
    ctx.strokeStyle = 'rgba(0,150,0,0.5)'
    ctx.lineWidth = 2

    const headTop = HEIGHT * 0.15
    const headBottom = HEIGHT * 0.65

    ctx.moveTo(0, headTop)
    ctx.lineTo(WIDTH, headTop)

    ctx.moveTo(0, headBottom)
    ctx.lineTo(WIDTH, headBottom)

    ctx.stroke()
}

upload.addEventListener('change',(e)=>{
    const file = e.target.files[0]
    if(!file) return

    const reader = new FileReader()
    reader.onload = ()=>{
        img = new Image()
        img.src = reader.result
        img.onload = ()=>{
            const ratio = Math.max(WIDTH / img.width, HEIGHT / img.height)
            scale = ratio
            offsetX = (WIDTH - img.width * scale) / 2
            offsetY = (HEIGHT - img.height * scale) / 2
            draw()
        }
    }
    reader.readAsDataURL(file)
})

canvas.addEventListener('mousedown',(e)=>{
    dragging = true
    startX = e.offsetX
    startY = e.offsetY
})

canvas.addEventListener('mousemove',(e)=>{
    if(!dragging) return

    const dx = e.offsetX - startX
    const dy = e.offsetY - startY

    offsetX += dx
    offsetY += dy

    startX = e.offsetX
    startY = e.offsetY

    draw()
})

window.addEventListener('mouseup',()=> dragging = false)

canvas.addEventListener('wheel',(e)=>{
    if(!img) return
    e.preventDefault()

    const zoom = e.deltaY > 0 ? -0.1 : 0.1
    const newScale = clamp(scale + zoom, 0.8, 3)

    const mouseX = e.offsetX
    const mouseY = e.offsetY

    const dx = mouseX - offsetX
    const dy = mouseY - offsetY

    const ratio = newScale / scale

    offsetX = mouseX - dx * ratio
    offsetY = mouseY - dy * ratio

    scale = newScale
    draw()
})

zoomIn.onclick = ()=>{
    scale = clamp(scale + 0.1, 0.8, 3)
    draw()
}

zoomOut.onclick = ()=>{
    scale = clamp(scale - 0.1, 0.8, 3)
    draw()
}

resetBtn.onclick = ()=>{
    if(!img) return

    const ratio = Math.max(WIDTH / img.width, HEIGHT / img.height)
    scale = ratio
    offsetX = (WIDTH - img.width * scale) / 2
    offsetY = (HEIGHT - img.height * scale) / 2

    draw()
}

saveBtn.addEventListener('click',()=>{
    if(!img) return
    hiddenInput.value = canvas.toDataURL('image/jpeg', 0.9)
})
