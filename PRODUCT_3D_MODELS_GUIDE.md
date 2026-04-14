# Product 3D Models Support

## Overview
The system now supports uploading 3D model files (.glb, .gltf, .obj, and .mtl) as product media alongside regular images. This enables more immersive product visualization and AR/VR experiences.

## Supported Formats
- **GLB** (.glb) - Binary glTF format (recommended for web)
- **GLTF** (.gltf) - Text-based glTF format
- **OBJ** (.obj) - Wavefront OBJ format (widely supported)
- **MTL** (.mtl) - Material Template Library (companion to OBJ files)

## Features

### 1. Upload 3D Models
- Navigate to product edit page: `http://127.0.0.1:8000/en/eg/admin/products/{id}/edit`
- You can now upload 3D models for:
  - **Main Product Image** - Primary product visualization
  - **Additional Images** - Gallery of images and 3D models
- Maximum file size: 10MB per file
- Supported formats: JPEG, PNG, JPG, WEBP, GLB, GLTF, OBJ, MTL

### 2. Admin Panel Display
- 3D models are displayed with a cube icon and file extension indicator
- Regular images show as thumbnails
- Both can be deleted using the X button

### 3. API Response
The API now includes a `media` array with detailed information about each file:

```json
{
  "media": [
    {
      "url": "http://example.com/storage/products/3d-models/model.glb",
      "type": "3d_model",
      "extension": "glb"
    },
    {
      "url": "http://example.com/storage/products/images/image.jpg",
      "type": "image",
      "extension": "jpg"
    }
  ]
}
```

### 4. Storage Structure
- Images are stored in: `storage/app/public/products/images/`
- 3D models are stored in: `storage/app/public/products/3d-models/`

## Frontend Integration

### For React/Next.js Applications

#### Using Three.js for GLB/GLTF Models

```bash
npm install three @react-three/fiber @react-three/drei
```

```tsx
import { Canvas } from '@react-three/fiber'
import { OrbitControls, useGLTF } from '@react-three/drei'

function Model({ url }: { url: string }) {
  const { scene } = useGLTF(url)
  return <primitive object={scene} />
}

function Product3DViewer({ mediaUrl }: { mediaUrl: string }) {
  return (
    <Canvas camera={{ position: [0, 0, 5] }}>
      <ambientLight intensity={0.5} />
      <spotLight position={[10, 10, 10]} angle={0.15} />
      <Model url={mediaUrl} />
      <OrbitControls />
    </Canvas>
  )
}
```

#### Using Three.js for OBJ Models

```tsx
import { useLoader } from '@react-three/fiber'
import { OBJLoader } from 'three/examples/jsm/loaders/OBJLoader'
import { MTLLoader } from 'three/examples/jsm/loaders/MTLLoader'

function OBJModel({ objUrl, mtlUrl }: { objUrl: string; mtlUrl?: string }) {
  const materials = mtlUrl ? useLoader(MTLLoader, mtlUrl) : null
  const obj = useLoader(OBJLoader, objUrl, (loader) => {
    if (materials) {
      loader.setMaterials(materials)
    }
  })
  
  return <primitive object={obj} />
}
```

#### Detecting Media Type

```tsx
function ProductGallery({ media }: { media: MediaItem[] }) {
  return (
    <div className="gallery">
      {media.map((item, index) => (
        <div key={index}>
          {item.type === '3d_model' ? (
            <Product3DViewer mediaUrl={item.url} />
          ) : (
            <img src={item.url} alt="Product" />
          )}
        </div>
      ))}
    </div>
  )
}
```

### For AR/VR Experiences

#### Using model-viewer (Google)

```html
<script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>

<model-viewer
  src="product-model.glb"
  alt="Product 3D Model"
  auto-rotate
  camera-controls
  ar
  ar-modes="webxr scene-viewer quick-look"
></model-viewer>
```

## Creating 3D Models

### Recommended Tools
1. **Blender** (Free, Open Source)
   - Export as GLB/GLTF
   - Optimize polygon count for web

2. **SketchUp** (Free/Paid)
   - Use GLB exporter plugin

3. **3ds Max / Maya** (Professional)
   - Export via Babylon.js exporter

### Best Practices
- Keep polygon count under 50,000 for web performance
- Use compressed textures (JPG for color, PNG for transparency)
- Optimize file size (aim for under 5MB)
- Test on mobile devices
- Include proper materials and lighting

### Online Conversion Tools
- **Sketchfab** - Convert and optimize 3D models
- **Blender** - Free 3D modeling and conversion
- **glTF Viewer** - Preview and validate GLB files

## API Endpoints

### Get Product with 3D Models
```
GET /api/v1/products/{slug}
```

Response includes both `images` (backward compatible) and `media` (with type info):
```json
{
  "images": ["url1", "url2"],
  "media": [
    {"url": "url1", "type": "image", "extension": "jpg"},
    {"url": "url2", "type": "3d_model", "extension": "glb"}
  ]
}
```

## Database Structure

### Attachments Table
- `type` field stores: `'additional_image'` or `'3d_model'`
- `path` field stores the file path
- Files are linked via polymorphic relationship to products

## Troubleshooting

### Upload Issues
- Ensure file size is under 10MB
- Check file extension is .glb or .gltf
- Verify storage permissions

### Display Issues
- Clear browser cache
- Check file path in database
- Verify file exists in storage folder

### Performance Issues
- Optimize 3D model polygon count
- Compress textures
- Use GLB instead of GLTF (binary is faster)
- Implement lazy loading for 3D viewers

## Future Enhancements
- [ ] 3D model preview in admin panel
- [ ] Automatic thumbnail generation for 3D models
- [ ] Model optimization on upload
- [ ] AR quick-look support for iOS
- [ ] WebXR integration for VR experiences
