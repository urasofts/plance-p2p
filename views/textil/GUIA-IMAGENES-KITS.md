 # 🎨 Guía de Implementación de Imágenes PNG - Premier League Kits

## ✅ ¿Qué se arregló?

### **Problema anterior:**
- La imagen en el checkout se veía muy grande y desproporcionada
- El diseño no se parecía a las tiendas profesionales de juegos

### **Solución implementada:**
1. ✅ **Checkout compacto** - Imagen de 64x64px al lado del nombre
2. ✅ **Cards optimizadas** - Imágenes de 56x56px centradas
3. ✅ **Sistema automático** - Detecta si la card tiene PNG o emoji
4. ✅ **Layout profesional** - Diseño similar a tiendas de COD, EA, eFootball

---

## 📐 Especificaciones de diseño

### **En las Cards de productos:**
```
┌─────────────────────┐
│  ┌─────────────┐    │
│  │   Imagen    │    │  ← 56x56px con borde redondeado
│  │   56x56px   │    │
│  └─────────────┘    │
│   Liverpool FC      │  ← Texto centrado
│  Kit completo 24/25 │
│    50.000 COP       │
└─────────────────────┘
```

### **En el Checkout:**
```
┌──────────────────────────────────┐
│ ┌────┐  Liverpool FC             │
│ │ 64 │  Kit completo 24/25       │ ← Imagen 64x64px + info
│ │ px │  Total: 50.000 COP        │
│ └────┘                           │
├──────────────────────────────────┤
│ [Resto del formulario...]        │
└──────────────────────────────────┘
```

---

## 🚀 Cómo agregar imágenes PNG (3 opciones)

### **OPCIÓN 1: Directo en el HTML (Más fácil)** ⭐ RECOMENDADO
```html
<div class="product-card__img">
    <img src="../assets/kits/premier-league/liverpool.png" alt="Liverpool FC">
</div>
```

**Ventajas:**
- ✅ Automático - el sistema detecta la imagen
- ✅ No necesitas tocar el JavaScript
- ✅ Funciona de inmediato

### **OPCIÓN 2: URLs externas**
```html
<div class="product-card__img">
    <img src="https://ejemplo.com/camiseta.png" alt="Liverpool FC">
</div>
```

### **OPCIÓN 3: Dejar emojis temporalmente**
```html
<div class="product-card__img">
    🔴
</div>
```

---

## 📸 Recomendaciones para las imágenes

### ✅ **IMÁGENES IDEALES:**

**Tamaño:**
- 200x200px a 400x400px (cuadradas)
- Máximo 300KB por imagen

**Formato:**
- PNG con fondo transparente
- SVG (opcional, aún mejor)

**Contenido:**
- Camiseta vista de frente
- Centrada en el canvas
- Fondo transparente
- Colores vibrantes

### ❌ **EVITAR:**

- ❌ Imágenes con fondo blanco/negro
- ❌ JPG (usar PNG)
- ❌ Imágenes muy pesadas (>500KB)
- ❌ Imágenes rectangulares muy alargadas
- ❌ Camisetas cortadas o descentradas

---

## 🎯 Ejemplo completo de implementación

### **Estructura de archivos sugerida:**
```
c:\xampp\htdocs\plance\
└── assets\
    └── kits\
        └── premier-league\
            ├── liverpool.png
            ├── man-city.png
            ├── man-united.png
            ├── chelsea.png
            ├── arsenal.png
            ├── west-ham.png
            ├── tottenham.png
            └── aston-villa.png
```

### **Código en pl.php (línea ~103):**
```html
<div class="product-card" data-id="1" data-producto="Kit Liverpool FC" data-precio="50000">
    <div class="product-card__img">
        <img src="../assets/kits/premier-league/liverpool.png" alt="Liverpool FC">
    </div>
    <div class="product-card__name">Liverpool FC</div>
    <div class="product-card__label">Kit completo · Temporada 24/25</div>
    <div class="product-card__price">50.000 COP</div>
</div>
```

---

## 🎨 Características CSS aplicadas

El sistema ya está configurado con:

```css
/* Cards de productos */
.product-card__img {
    width: 56px;
    height: 56px;
    object-fit: contain;  ← Mantiene proporciones
    border-radius: 8px;
    background: var(--bg-base);
}

/* Checkout */
.checkout-product-img {
    width: 64px;
    height: 64px;
    object-fit: contain;  ← No distorsiona la imagen
    border-radius: 10px;
}
```

**`object-fit: contain`** es la clave:
- ✅ Mantiene las proporciones
- ✅ No corta la imagen
- ✅ No la distorsiona
- ✅ Centra automáticamente

---

## 🔧 Sistema automático JavaScript

El código detecta automáticamente si una card tiene imagen:

```javascript
// ✅ Si tiene <img> → copia la imagen al checkout
// ✅ Si solo tiene emoji → copia el emoji al checkout

updateCheckoutFromCard(card) {
    const imgElement = cardImg.querySelector('img');
    if (imgElement) {
        // Usar imagen PNG
    } else {
        // Usar emoji
    }
}
```

**No necesitas configurar nada en el JavaScript** - solo agrega las imágenes en el HTML y funcionará automáticamente.

---

## 📱 Responsive

El diseño es responsive:
- **Desktop**: 4 columnas de productos
- **Tablet**: 3 columnas
- **Móvil**: 2 columnas

Las imágenes se adaptan automáticamente al tamaño de la card.

---

## 🎯 Resultado final

El diseño ahora es:
- ✅ Compacto y profesional
- ✅ Similar a tiendas de COD, EA Sports, eFootball
- ✅ Imágenes con tamaño perfecto
- ✅ Checkout limpio con imagen al lado
- ✅ Sistema automático que detecta PNG o emojis

---

## ⚡ Próximos pasos

1. **Consigue o crea las imágenes PNG** de las camisetas
2. **Guárdalas** en `assets/kits/premier-league/`
3. **Reemplaza** el emoji por `<img>` en cada card
4. **¡Listo!** - El sistema las detectará automáticamente

---

## 🆘 Ayuda

Si necesitas ayuda con:
- Encontrar/crear las imágenes PNG
- Optimizar el tamaño de las imágenes
- Ajustar más el diseño
- Agregar más equipos

Solo dime y te ayudo! 🚀
