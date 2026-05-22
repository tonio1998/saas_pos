import $ from 'jquery'

window.$ = window.jQuery = $

const FRAME_WIDTH = 260
const FRAME_HEIGHT = 330

const canvas = document.getElementById('photoCanvas')
const ctx = canvas.getContext('2d')

canvas.width = FRAME_WIDTH
canvas.height = FRAME_HEIGHT

const upload = document.getElementById('uploadPhoto')
const zoomIn = document.getElementById('zoomIn')
const zoomOut = document.getElementById('zoomOut')
const resetBtn = document.getElementById('resetPhoto')
const saveBtn = document.getElementById('saveBtn')
const croppedInput = document.getElementById('croppedPhoto')

const currentPhoto = document.getElementById('currentPhoto')?.value || ''

let img = new Image()

let scale = 1
let minScale = 1

let posX = 0
let posY = 0

let isDragging = false
let startX = 0
let startY = 0

function fitImage() {
    if (!img.width || !img.height) return

    const imageRatio = img.width / img.height
    const frameRatio = FRAME_WIDTH / FRAME_HEIGHT

    if (imageRatio > frameRatio) {
        scale = FRAME_HEIGHT / img.height
    } else {
        scale = FRAME_WIDTH / img.width
    }

    minScale = scale

    const width = img.width * scale
    const height = img.height * scale

    posX = (FRAME_WIDTH - width) / 2
    posY = (FRAME_HEIGHT - height) / 2

    draw()
}

function clampPosition() {
    const width = img.width * scale
    const height = img.height * scale

    if (width <= FRAME_WIDTH) {
        posX = (FRAME_WIDTH - width) / 2
    } else {
        const minX = FRAME_WIDTH - width
        const maxX = 0

        posX = Math.min(maxX, Math.max(minX, posX))
    }

    if (height <= FRAME_HEIGHT) {
        posY = (FRAME_HEIGHT - height) / 2
    } else {
        const minY = FRAME_HEIGHT - height
        const maxY = 0

        posY = Math.min(maxY, Math.max(minY, posY))
    }
}

function draw() {
    ctx.clearRect(0, 0, FRAME_WIDTH, FRAME_HEIGHT)

    if (!img.src) return

    clampPosition()

    const width = img.width * scale
    const height = img.height * scale

    ctx.drawImage(img, posX, posY, width, height)
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
        console.error('Failed to load image')
        alert('Failed to load image')
    }

    temp.src = src
}

function applyZoom(factor) {
    const newScale = scale * factor

    if (newScale < minScale) return

    const centerX = FRAME_WIDTH / 2
    const centerY = FRAME_HEIGHT / 2

    posX = centerX - (centerX - posX) * (newScale / scale)
    posY = centerY - (centerY - posY) * (newScale / scale)

    scale = newScale

    draw()
}

if (currentPhoto) {
    loadImage(currentPhoto)
}

upload.addEventListener('change', e => {
    const file = e.target.files?.[0]

    if (!file) return

    if (!file.type.startsWith('image/')) {
        alert('Please select a valid image')
        return
    }

    const reader = new FileReader()

    reader.onload = ev => {
        loadImage(ev.target.result)
    }

    reader.onerror = () => {
        alert('Failed to read image')
    }

    reader.readAsDataURL(file)
})

canvas.addEventListener('mousedown', e => {
    if (!img.src) return

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

canvas.addEventListener('mouseup', () => {
    isDragging = false
})

canvas.addEventListener('mouseleave', () => {
    isDragging = false
})

canvas.addEventListener(
    'wheel',
    e => {
        e.preventDefault()

        if (!img.src) return

        applyZoom(e.deltaY < 0 ? 1.05 : 0.95)
    },
    { passive: false }
)

zoomIn.addEventListener('click', () => {
    if (!img.src) return

    applyZoom(1.1)
})

zoomOut.addEventListener('click', () => {
    if (!img.src) return

    applyZoom(0.9)
})

resetBtn.addEventListener('click', () => {
    if (currentPhoto) {
        loadImage(currentPhoto)
        return
    }

    ctx.clearRect(0, 0, FRAME_WIDTH, FRAME_HEIGHT)

    img = new Image()

    scale = 1
    minScale = 1

    posX = 0
    posY = 0
})

saveBtn.addEventListener('click', e => {
    if (!img.src) {
        e.preventDefault()

        alert('Please upload a photo first')

        return
    }

    try {
        const cropped = canvas.toDataURL('image/jpeg', 1)

        croppedInput.value = cropped
    } catch (err) {
        e.preventDefault()

        console.error(err)

        alert('Failed to process image')
    }
})
