      
      # Imágenes de Kits - Premier League

## 📂 Estructura de carpetas
Coloca tus imágenes PNG en esta carpeta con los siguientes nombres:

```
assets/kits/premier-league/
├── liverpool.png
├── man-city.png
├── man-united.png
├── chelsea.png
├── arsenal.png
├── west-ham.png
├── tottenham.png
└── aston-villa.png
```

## 📐 Recomendaciones de tamaño

### Para las cards de productos:
- **Tamaño recomendado**: 200x200px o 300x300px
- **Formato**: PNG con fondo transparente
- **Resolución**: 72-150 DPI

### Para el checkout:
- El mismo archivo se redimensionará automáticamente a 64x64px
- object-fit: contain (mantiene proporciones)

## 🎨 Características de las imágenes

✅ **DEBE SER**:
- Fondo transparente
- Formato PNG
- Camiseta centrada
- Colores vibrantes

❌ **EVITAR**:
- Fondos blancos o de color
- Imágenes muy grandes (más de 500KB)
- JPG (usar PNG)

## 🔧 Cómo implementar

Ya está todo listo en el código. Solo necesitas:

1. **Guardar tus imágenes PNG** en esta carpeta con los nombres indicados arriba
2. **Actualizar el objeto `products`** en `pl.php` línea ~260:

```javascript
const products = {
    1:{name:'Liverpool FC', img:'🔴', imgSrc:'../assets/kits/premier-league/liverpool.png', producto:'Kit Liverpool FC'},
    2:{name:'Manchester City', img:'🔵', imgSrc:'../assets/kits/premier-league/man-city.png', producto:'Kit Manchester City'},
    // ... etc
};
```

3. **Opcional**: También puedes agregar las imágenes directamente en las cards HTML:

```html
<div class="product-card__img">
    <img src="../assets/kits/premier-league/liverpool.png" alt="Liverpool FC">
</div>
```

El sistema funciona con **ambos métodos** (emojis o PNG). Si `imgSrc` tiene una URL, usará la imagen; si está vacío, usará el emoji.
