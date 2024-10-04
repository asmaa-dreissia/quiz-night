import { neonCursor } from 'https://unpkg.com/threejs-toys@0.0.8/build/threejs-toys.module.cdn.min.js';

neonCursor({
    el: document.getElementById('app'),
    shaderPoints: 16,
    curvePoints: 80,
    curveLerp: 0.5,
    radius1: 2,  // Taille initiale du cercle
    radius2: 10, // Taille finale du cercle
    velocityTreshold: 10,
    sleepRadiusX: 50, // Zone de rotation en largeur
    sleepRadiusY: 50, // Zone de rotation en hauteur
    sleepTimeCoefX: 0.0025,
    sleepTimeCoefY: 0.0025,
    colors: [0xff0000, 0x00ff00, 0x0000ff, 0xffff00, 0xff00ff, 0x00ffff], // Couleurs vives
    intensity: 2, // Augmente l'intensité pour rendre les couleurs plus vives
    noiseIntensity: 0.5 // Ajuste l'intensité du bruit pour un effet plus net
});