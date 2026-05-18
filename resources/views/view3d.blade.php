@extends('layouts.app')

@section('title', '3D Model View')
@section('page-title', '3D MODEL VIEW')
@section('page-sub', 'Lihat dan analisis objek 3D secara interaktif dari berkas ekspor Tinkercad.')

@section('styles')
<style>
    /* Custom styles for view3d page */
    .view3d-container {
        display: flex;
        gap: 1.5rem;
        height: calc(100vh - 180px);
    }
    
    .canvas-wrapper {
        flex: 1;
        background: linear-gradient(135deg, #111, #1a1a1a);
        border: 1px solid #262626;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
    }
    
    #canvas3d {
        width: 100%;
        height: 100%;
        display: block;
    }
    
    .control-sidebar {
        width: 320px;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        overflow-y: auto;
    }
    
    .panel-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 12px;
        padding: 1.25rem;
    }
    
    .panel-card-title {
        font-size: 1rem;
        font-weight: 600;
        margin: 0 0 1rem 0;
        color: #ededed;
    }
    
    .control-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .btn-control {
        background: #262626;
        color: #ededed;
        border: 1px solid transparent;
        padding: 0.75rem;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    
    .btn-control:hover {
        background: #333;
        border-color: #444;
    }
    
    .btn-control.active {
        border-color: var(--warna-utama, #10b981);
        color: var(--warna-utama, #10b981);
        background: rgba(16, 185, 129, 0.05);
    }
    
    .slider-group {
        margin-top: 1rem;
    }
    
    .slider-label {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        color: #9ca3af;
        margin-bottom: 0.5rem;
    }
    
    .slider-input {
        width: 100%;
        accent-color: var(--warna-utama, #10b981);
    }
    
    .instruction-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .instruction-item {
        display: flex;
        gap: 0.75rem;
        font-size: 0.8rem;
        color: #9ca3af;
    }
    
    .instruction-item i {
        color: var(--warna-utama, #10b981);
        margin-top: 0.25rem;
    }
    
    .info-table {
        width: 100%;
        font-size: 0.8rem;
        border-collapse: collapse;
    }
    
    .info-table td {
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    
    .info-label { color: #9ca3af; }
    .info-value { text-align: right; color: #ededed; font-weight: 500; }
    
    /* Loader */
    .loader-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(10,10,10,0.9);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 10;
        transition: opacity 0.5s ease;
    }
    
    .loader-overlay.fade-out {
        opacity: 0;
        pointer-events: none;
    }
    
    .spinner-modern {
        width: 40px; height: 40px;
        border: 3px solid rgba(255,255,255,0.05);
        border-top-color: var(--warna-utama, #10b981);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 1rem;
    }
    
    @keyframes spin { to { transform: rotate(360deg); } }
    
    .progress-text { font-size: 0.9rem; color: #ededed; margin-bottom: 0.5rem; }
    
    .progress-bar-container {
        width: 200px;
        height: 4px;
        background: rgba(255,255,255,0.05);
        border-radius: 2px;
        overflow: hidden;
    }
    
    .progress-bar-fill {
        height: 100%;
        background: var(--warna-utama, #10b981);
        width: 0%;
        transition: width 0.3s ease;
    }
    
    @media (max-width: 1024px) {
        .view3d-container { flex-direction: column; height: auto; }
        .canvas-wrapper { height: 400px; }
        .control-sidebar { width: 100%; }
        .control-grid { grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); }
    }
    
    .desktop-only { display: block; }
    .mobile-only { display: none; }
    
    @media (max-width: 768px) {
        .desktop-only { display: none; }
        .mobile-only { display: block; }
    }
</style>
@endsection

@section('content')
<!-- 3D VIEWER WORKSPACE -->
<div class="view3d-container">
    
    <!-- CANVAS AREA -->
    <div class="canvas-wrapper">
        <!-- LOADING OVERLAY -->
        <div class="loader-overlay" id="loader-overlay">
            <div class="spinner-modern"></div>
            <div class="progress-text" id="progress-text">Memuat Model: 0%</div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" id="progress-bar-fill"></div>
            </div>
        </div>
        
        <canvas id="canvas3d"></canvas>
    </div>

    <!-- CONTROL PANEL SIDEBAR -->
    <div class="control-sidebar">
        
        <!-- UTILITY CONTROLS -->
        <div class="panel-card">
            <h3 class="panel-card-title">Kontrol Viewport</h3>
            <div class="control-grid">
                <button class="btn-control" id="btn-reset">
                    Reset Kamera <i class="fa-solid fa-camera"></i>
                </button>
                <button class="btn-control" id="btn-rotate">
                    Rotasi Otomatis <i class="fa-solid fa-arrows-spin"></i>
                </button>
                <button class="btn-control" id="btn-wireframe">
                    Mode Wireframe <i class="fa-solid fa-border-none"></i>
                </button>
            </div>

            <!-- SLIDER PENCALAHAN -->
            <div class="slider-group">
                <div class="slider-label">
                    <span>Intensitas Cahaya</span>
                    <span id="light-val">1.2x</span>
                </div>
                <input type="range" id="slider-light" class="slider-input" min="0.2" max="3" step="0.1" value="1.2">
            </div>
        </div>

        <!-- INTERACTION HELP -->
        <div class="panel-card">
            <!-- Desktop Header -->
            <h3 class="panel-card-title desktop-only">Navigasi Mouse</h3>
            <div class="instruction-list desktop-only">
                <div class="instruction-item">
                    <i class="fa-solid fa-mouse-pointer"></i>
                    <span><strong>Klik & Seret (Kiri):</strong> Putar/Rotasi objek 3D secara bebas.</span>
                </div>
                <div class="instruction-item">
                    <i class="fa-solid fa-computer-mouse"></i>
                    <span><strong>Scroll Wheel:</strong> Perbesar (Zoom In) atau perkecil (Zoom Out).</span>
                </div>
                <div class="instruction-item">
                    <i class="fa-solid fa-hand"></i>
                    <span><strong>Klik & Seret (Kanan):</strong> Geser kamera (Panning) ke segala arah.</span>
                </div>
            </div>

            <!-- Mobile Header -->
            <h3 class="panel-card-title mobile-only">Navigasi Sentuh</h3>
            <div class="instruction-list mobile-only">
                <div class="instruction-item">
                    <i class="fa-solid fa-fingerprint"></i>
                    <span><strong>Satu Jari:</strong> Putar/Rotasi objek 3D secara bebas.</span>
                </div>
                <div class="instruction-item">
                    <i class="fa-solid fa-up-down-left-right"></i>
                    <span><strong>Cubit (Pinch):</strong> Perbesar (Zoom In) atau perkecil (Zoom Out).</span>
                </div>
                <div class="instruction-item">
                    <i class="fa-solid fa-up-right-from-square"></i>
                    <span><strong>Dua Jari:</strong> Geser kamera (Panning) ke segala arah.</span>
                </div>
            </div>
        </div>

        <!-- OBJECT INFORMATION -->
        <div class="panel-card">
            <h3 class="panel-card-title">Informasi Model</h3>
            <table class="info-table">
                <tr>
                    <td class="info-label">Nama Berkas</td>
                    <td class="info-value">tinker.obj</td>
                </tr>
                <tr>
                    <td class="info-label">Material (.mtl)</td>
                    <td class="info-value">obj.mtl</td>
                </tr>
                <tr>
                    <td class="info-label">Format File</td>
                    <td class="info-value">Wavefront OBJ</td>
                </tr>
                <tr>
                    <td class="info-label">Renderer Engine</td>
                    <td class="info-value">Three.js WebGL</td>
                </tr>
            </table>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<!-- LOAD THREE.JS FROM CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/MTLLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/OBJLoader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>

<!-- THREE.JS ENGINE LOGIC -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('canvas3d');
        const wrapper = canvas.parentElement;
        
        // Elements
        const loaderOverlay = document.getElementById('loader-overlay');
        const progressText = document.getElementById('progress-text');
        const progressBarFill = document.getElementById('progress-bar-fill');
        const btnReset = document.getElementById('btn-reset');
        const btnRotate = document.getElementById('btn-rotate');
        const btnWireframe = document.getElementById('btn-wireframe');
        const sliderLight = document.getElementById('slider-light');
        const lightValText = document.getElementById('light-val');

        // Constants
        const mtlPath = "{{ asset('3d/obj.mtl') }}";
        const objPath = "{{ asset('3d/tinker.obj') }}";

        // State
        let scene, camera, renderer, controls;
        let loadedObject = null;
        let autoRotate = false;
        let wireframeMode = false;
        let ambientLight, dirLight, dirLight2;
        let defaultCameraPosition = { x: 0, y: 0, z: 10 };
        let defaultCameraTarget = { x: 0, y: 0, z: 0 };

        // Initialize Three.js Scene
        function init() {
            scene = new THREE.Scene();
            scene.background = null; // transparent background to let wrapper CSS gradient shine through

            // Camera Setup
            camera = new THREE.PerspectiveCamera(
                45,
                wrapper.clientWidth / wrapper.clientHeight,
                1,
                1000
            );

            // Renderer Setup
            renderer = new THREE.WebGLRenderer({ canvas: canvas, antialias: true, alpha: true });
            renderer.setSize(wrapper.clientWidth, wrapper.clientHeight);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;

            // Orbit Controls
            controls = new THREE.OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.dampingFactor = 0.05;
            controls.maxPolarAngle = Math.PI / 2 + 0.1; // allow looking slightly from below

            // Lighting
            ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
            scene.add(ambientLight);

            dirLight = new THREE.DirectionalLight(0xffffff, 0.8);
            dirLight.position.set(5, 10, 7);
            dirLight.castShadow = true;
            dirLight.shadow.mapSize.width = 1024;
            dirLight.shadow.mapSize.height = 1024;
            dirLight.shadow.bias = -0.001;
            scene.add(dirLight);

            dirLight2 = new THREE.DirectionalLight(0x90e0ef, 0.3); // subtle blue fill light
            dirLight2.position.set(-5, -5, -5);
            scene.add(dirLight2);

            // Load Model
            loadModel();

            // Event Listeners
            window.addEventListener('resize', onWindowResize);
            setupUIControls();

            // Start Loop
            animate();
        }

        // Load OBJ and MTL
        function loadModel() {
            const mtlLoader = new THREE.MTLLoader();
            
            const mtlBaseUrl = mtlPath.substring(0, mtlPath.lastIndexOf('/') + 1);
            const mtlFileName = mtlPath.substring(mtlPath.lastIndexOf('/') + 1);
            
            const objBaseUrl = objPath.substring(0, objPath.lastIndexOf('/') + 1);
            const objFileName = objPath.substring(objPath.lastIndexOf('/') + 1);

            progressText.innerText = "Mengunduh Material (MTL)...";

            mtlLoader.setPath(mtlBaseUrl);
            mtlLoader.load(mtlFileName, function (materials) {
                materials.preload();
                
                const objLoader = new THREE.OBJLoader();
                objLoader.setMaterials(materials);
                objLoader.setPath(objBaseUrl);
                
                objLoader.load(objFileName, 
                    // On Success
                    function (object) {
                        loadedObject = object;
                        scene.add(object);

                        const box = new THREE.Box3().setFromObject(object);
                        const size = box.getSize(new THREE.Vector3());
                        const center = box.getCenter(new THREE.Vector3());

                        object.position.x = -center.x;
                        object.position.y = -center.y;
                        object.position.z = -center.z;

                        const maxDim = Math.max(size.x, size.y, size.z);
                        const fov = camera.fov * (Math.PI / 180);
                        let cameraZ = Math.abs(maxDim / 2 / Math.tan(fov / 2));
                        
                        cameraZ *= 1.4;
                        
                        defaultCameraPosition = { x: 0, y: maxDim * 0.1, z: cameraZ };
                        camera.position.set(defaultCameraPosition.x, defaultCameraPosition.y, defaultCameraPosition.z);
                        
                        camera.lookAt(0, 0, 0);
                        controls.target.set(0, 0, 0);
                        controls.update();

                        loaderOverlay.classList.add('fade-out');
                    },
                    // On Progress
                    function (xhr) {
                        if (xhr.lengthComputable) {
                            const percentComplete = (xhr.loaded / xhr.total) * 100;
                            const rounded = Math.round(percentComplete);
                            progressText.innerText = `Memuat Model: ${rounded}%`;
                            progressBarFill.style.width = `${percentComplete}%`;
                        } else {
                            progressText.innerText = "Memuat Objek 3D...";
                        }
                    },
                    // On Error
                    function (error) {
                        progressText.innerText = "Gagal memuat model 3D.";
                        console.error(error);
                    }
                );
            });
        }

        function onWindowResize() {
            camera.aspect = wrapper.clientWidth / wrapper.clientHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(wrapper.clientWidth, wrapper.clientHeight);
        }

        function setupUIControls() {
            btnReset.addEventListener('click', () => {
                camera.position.set(defaultCameraPosition.x, defaultCameraPosition.y, defaultCameraPosition.z);
                controls.target.set(defaultCameraTarget.x, defaultCameraTarget.y, defaultCameraTarget.z);
                controls.update();
            });

            btnRotate.addEventListener('click', () => {
                autoRotate = !autoRotate;
                btnRotate.classList.toggle('active', autoRotate);
            });

            btnWireframe.addEventListener('click', () => {
                wireframeMode = !wireframeMode;
                btnWireframe.classList.toggle('active', wireframeMode);
                
                if (loadedObject) {
                    loadedObject.traverse((child) => {
                        if (child.isMesh) {
                            if (Array.isArray(child.material)) {
                                child.material.forEach(mat => mat.wireframe = wireframeMode);
                            } else if (child.material) {
                                child.material.wireframe = wireframeMode;
                            }
                        }
                    });
                }
            });

            sliderLight.addEventListener('input', (e) => {
                const value = parseFloat(e.target.value);
                lightValText.innerText = `${value.toFixed(1)}x`;
                dirLight.intensity = value * 0.65;
                ambientLight.intensity = value * 0.5;
            });
        }

        // Animation Loop
        function animate() {
            requestAnimationFrame(animate);

            if (autoRotate && loadedObject) {
                loadedObject.rotation.y += 0.005;
            }

            controls.update();
            renderer.render(scene, camera);
        }

        // Run Init
        init();
    });
</script>
@endsection
