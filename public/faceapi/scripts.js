// =====================================
// GLOBAL VARIABLES
// =====================================
let labeledFaceDescriptors = [];
let faceMatcher;
const FACE_MATCH_THRESHOLD = 0.5;


// =====================================
// LOAD DATABASE
// =====================================
async function loadFaceDatabase() {
    const res = await fetch("/faceapi/user-image");
    const data = await res.json();

    if (!data.photo_url) {
        console.error("Foto tidak ditemukan di student_details");
        return;
    }

    const img = await faceapi.fetchImage(data.photo_url);

    const detection = await faceapi
        .detectSingleFace(img)
        .withFaceLandmarks()
        .withFaceDescriptor();

    if (!detection) {
        console.warn("Wajah tidak terdeteksi dari foto database");
        return;
    }

    labeledFaceDescriptors.push(
        new faceapi.LabeledFaceDescriptors(data.name, [detection.descriptor])
    );

    // Perketat threshold
    faceMatcher = new faceapi.FaceMatcher(labeledFaceDescriptors, FACE_MATCH_THRESHOLD);
}



// =====================================
// MAIN PROGRAM
// =====================================
async function run() {
    await Promise.all([
        faceapi.nets.ssdMobilenetv1.loadFromUri('/faceapi/models'),
        faceapi.nets.faceLandmark68Net.loadFromUri('/faceapi/models'),
        faceapi.nets.faceRecognitionNet.loadFromUri('/faceapi/models'),
        faceapi.nets.faceExpressionNet.loadFromUri('/faceapi/models'),
    ]);

    console.log("Models loaded!");

    await loadFaceDatabase();
    console.log("Face database loaded!");

    const video = document.getElementById("video");
    const canvas = document.getElementById("canvas");


    // Kamera langsung aktif tanpa tombol
    video.style.display = "block";
    navigator.mediaDevices.getUserMedia({ video: {} })
        .then(stream => {
            video.srcObject = stream;
        })
        .catch(err => {
            console.error("Gagal mengakses kamera:", err);
        });

    // canvas di-resize saat metadata video sudah siap
    video.addEventListener("loadedmetadata", () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
    });

    video.addEventListener("playing", () => {
        if (video.videoWidth === 0 || video.videoHeight === 0) {
            console.warn("Video belum siap, dimensi 0");
            return;
        }
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext("2d");


        // Ambil elemen label, deskripsi, dan gambar ekspresi di kontainer kiri
        const ekspresiLabel = document.querySelector('.absen-left h2');
        const ekspresiDesc = document.querySelector('.absen-left p');
        // Tambahkan elemen baru untuk ekspresi terdeteksi
        let ekspresiDetected = document.querySelector('.absen-left .ekspresi-terdeteksi');
        if (!ekspresiDetected && ekspresiDesc) {
            ekspresiDetected = document.createElement('div');
            ekspresiDetected.className = 'ekspresi-terdeteksi text-slate-400 mt-2';
            ekspresiDesc.parentNode.appendChild(ekspresiDetected);
        }

        setInterval(async () => {
            if (video.videoWidth === 0 || video.videoHeight === 0) return;

            const detections = await faceapi.detectAllFaces(video)
                .withFaceLandmarks()
                .withFaceDescriptors()
                .withFaceExpressions();

            const resized = faceapi.resizeResults(detections, {
                width: video.videoWidth,
                height: video.videoHeight
            });

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            let ekspresiTerdeteksi = null;
            let confidence = 0;
            let faceLabel = null;

            resized.forEach(face => {
                // -------------------------
                //  FACE RECOGNITION
                // -------------------------
                const bestMatch = faceMatcher.findBestMatch(face.descriptor);
                faceLabel = bestMatch.label;
                if (bestMatch.distance > FACE_MATCH_THRESHOLD) {
                    faceLabel = "unknown";
                }

                const box = face.detection.box;
                const drawBox = new faceapi.draw.DrawBox(box, {
                    label: faceLabel // <-- perbaiki dari 'label' ke 'faceLabel'
                });
                drawBox.draw(canvas);

                // -------------------------
                //  DETEKSI EKSPRESI
                // -------------------------
                const exp = face.expressions;

                // Cari ekspresi dominan
                const dominant = Object.entries(exp).sort((a,b)=>b[1]-a[1])[0];
                ekspresiTerdeteksi = dominant[0];
                confidence = dominant[1];
            });

            // Jangan update gambar/label ekspresi di kontainer kiri!
            // Hanya update deskripsi confidence ekspresi jika ingin
            if (ekspresiDetected && ekspresiTerdeteksi) {
                ekspresiDetected.textContent = `Ekspresi terdeteksi: ${ekspresiTerdeteksi.charAt(0).toUpperCase() + ekspresiTerdeteksi.slice(1)} (Confidence: ${(confidence*100).toFixed(1)}%)`;
            }

            // === Tambahan: cek ekspresi benar selama 3 detik ===
            if (window.absenEkspresiCheck) {
                window.absenEkspresiCheck(ekspresiTerdeteksi, confidence, faceLabel);
            }
        }, 150);
    });
}

run();
