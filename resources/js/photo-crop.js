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
const croppedInput = document.getElementById('croppedPhoto')

const currentPhoto = document.getElementById('currentPhoto')?.value

let img = new Image()
let scale = 1
let minScale = 1
let posX = 0
let posY = 0
let isDragging = false
let startX = 0
let startY = 0

function draw() {
    ctx.clearRect(0, 0, WIDTH, HEIGHT)

    if (!img.src) return

    const w = img.width * scale
    const h = img.height * scale

    ctx.drawImage(img, posX, posY, w, h)
}

function fitImage() {
    if (!img.width || !img.height) return

    const scaleX = WIDTH / img.width
    const scaleY = HEIGHT / img.height

    minScale = Math.max(scaleX, scaleY)
    scale = minScale

    const w = img.width * scale
    const h = img.height * scale

    posX = (WIDTH - w) / 2
    posY = (HEIGHT - h) / 2

    draw()
}

function loadImage(src) {
    if (!src) return

    const temp = new Image()
    temp.crossOrigin = 'anonymous'

    temp.onload = () => {
        img = temp
        fitImage()
    }

    temp.onerror = () => {
        console.error('Image load failed')
    }

    temp.src = src
}

/* =========================
   INIT (LOAD CURRENT PHOTO)
========================= */
if (currentPhoto) {
    loadImage(currentPhoto)
}

/* =========================
   FILE UPLOAD
========================= */
upload.addEventListener('change', e => {
    const file = e.target.files[0]
    if (!file) return

    const reader = new FileReader()

    reader.onload = ev => {
        loadImage(ev.target.result)
    }

    reader.readAsDataURL(file)
})

/* =========================
   DRAG
========================= */
canvas.addEventListener('mousedown', e => {
    isDragging = true
    startX = e.offsetX - posX
    startY = e.offsetY - posY
})

canvas.addEventListener('mousemove', e => {
    if (!isDragging) return

    posX = e.offsetX - startX
    posY = e.offsetY - startY

    draw()
})

canvas.addEventListener('mouseup', () => isDragging = false)
canvas.addEventListener('mouseleave', () => isDragging = false)

/* =========================
   ZOOM
========================= */
function applyZoom(factor) {
    const newScale = scale * factor
    if (newScale < minScale) return

    const centerX = WIDTH / 2
    const centerY = HEIGHT / 2

    posX = centerX - (centerX - posX) * (newScale / scale)
    posY = centerY - (centerY - posY) * (newScale / scale)

    scale = newScale
    draw()
}

zoomIn.addEventListener('click', () => applyZoom(1.1))
zoomOut.addEventListener('click', () => applyZoom(0.9))

canvas.addEventListener('wheel', e => {
    e.preventDefault()
    applyZoom(e.deltaY < 0 ? 1.05 : 0.95)
})

/* =========================
   RESET
========================= */
resetBtn.addEventListener('click', () => {
    if (currentPhoto) {
        loadImage(currentPhoto)
    } else {
        ctx.clearRect(0, 0, WIDTH, HEIGHT)
    }
})

/* =========================
   SAVE
========================= */
saveBtn.addEventListener('click', e => {
    if (!img.src) {
        e.preventDefault()
        alert('Please upload or use a photo first')
        return
    }

    try {
        const data = canvas.toDataURL('image/jpeg', 0.9)
        croppedInput.value = data
    } catch (err) {
        e.preventDefault()
        console.error(err)
        alert('Failed to process image')
    }
})
