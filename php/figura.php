<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modelo 3D con Interacciones</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/110/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.110.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.110.0/examples/js/loaders/GLTFLoader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.110.0/examples/js/geometries/TextGeometry.js"></script>
    <style>
        #regresarBtn {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background-color: #9B4DFF; 
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        #regresarBtn:hover {
            background-color: #7a3ce5;
        }
    </style>
</head>
<body style="margin: 0; overflow: hidden;">
   
    <button id="regresarBtn" onclick="window.location.href='crud.php'">Regresar</button>

    <script>
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer();
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.body.appendChild(renderer.domElement);

    
        scene.background = new THREE.Color(0xD8B4FF);

        const ambientLight = new THREE.AmbientLight(0xffffff, 1);
        scene.add(ambientLight);
        const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
        directionalLight.position.set(5, 10, 7.5);
        scene.add(directionalLight);

        const loader = new THREE.GLTFLoader();
        let model, cube;
        loader.load(
            'http://localhost/proyecto2/static/models/modelo.glb', 
            (gltf) => {
                model = gltf.scene;
                model.scale.set(0.04, 0.04, 0.04); 
                model.position.set(0, -4, 1); 
                scene.add(model);

                
                const cubeGeometry = new THREE.BoxGeometry(0.5, 0.5, 0.5);
                const cubeMaterial = [
                    new THREE.MeshBasicMaterial({ color: 0x9900FF }), 
                    new THREE.MeshBasicMaterial({ color: 0x9900FF }), 
                    new THREE.MeshBasicMaterial({ color: 0x9900FF }), 
                    new THREE.MeshBasicMaterial({ color: 0x9900FF }), 
                    new THREE.MeshBasicMaterial({ color: 0x9900FF }), 
                    new THREE.MeshBasicMaterial({ color: 0x9900FF })  
                ];
                cube = new THREE.Mesh(cubeGeometry, cubeMaterial);
                cube.position.set(0, 1.2, 0.8); 
                scene.add(cube);
                console.log('Modelo y cubo cargados correctamente');
            },
            (xhr) => {
                console.log(`Cargando modelo: ${(xhr.loaded / xhr.total) * 100}%`);
            },
            (error) => {
                console.error('Error al cargar el modelo:', error);
            }
        );

    
        camera.position.set(-0.8, -0.3, 3.2);

       
        const controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableZoom = true;
        controls.enableRotate = true;

       
        const raycaster = new THREE.Raycaster();
        const pointer = new THREE.Vector2();

        let cuboInfo = {};

      
        function cargarInformacionCubo() {
            fetch('get_cubo_info.php')
                .then(response => response.json())
                .then(data => {
                   
                    cuboInfo = {};
                    data.forEach(item => {
                        cuboInfo[`cara${item.cara}`] = item.descripcion;
                    });
                    console.log(cuboInfo); 
                })
                .catch(error => console.error('Error al cargar información del cubo:', error));
        }

   
        cargarInformacionCubo();

     
        window.addEventListener('pointerdown', (event) => {
            pointer.x = (event.clientX / window.innerWidth) * 2 - 1;
            pointer.y = -(event.clientY / window.innerHeight) * 2 + 1;
            raycaster.setFromCamera(pointer, camera);

            const intersects = raycaster.intersectObject(cube);
            if (intersects.length > 0) {
                const faceIndex = intersects[0].faceIndex;
                mostrarInformacion(faceIndex); 
            }
        });       
        function mostrarInformacion(faceIndex) {
             console.log("Índice de cara:", faceIndex);  
         if (faceIndex >= 0 && faceIndex < 6) {
        const info = cuboInfo[`cara${faceIndex + 1}`] || 'Sin información disponible';
        alert(`Cara ${faceIndex + 1}: ${info}`);
    }
}
        function animate() {
            requestAnimationFrame(animate);
            if (cube) {
                cube.rotation.x += 0.01;
                cube.rotation.y += 0.01;
            }
            controls.update();
            renderer.render(scene, camera);
        }
        animate();
    </script>
</body>
</html>
