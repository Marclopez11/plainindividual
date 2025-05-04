<svg xmlns="http://www.w3.org/2000/svg" {{ $attributes }} viewBox="0 0 512 100" fill="none">
    <g transform="translate(0, 5)">
        <!-- Icono del logo -->
        <svg x="0" y="0" width="90" height="90" viewBox="0 0 512 512">
            <!-- Círculo de fondo con gradiente -->
            <circle cx="256" cy="256" r="256" fill="url(#logoGrad)" />

            <!-- Icono de documento o plan -->
            <path d="M374.667 128H256c-5.891 0-10.667 4.776-10.667 10.667v74.667c0 5.891 4.776 10.667 10.667 10.667h128c5.891 0 10.667-4.776 10.667-10.667v-74.667c0-5.891-4.776-10.667-10.667-10.667z" fill="white" />
            <path d="M330.667 245.333H256c-5.891 0-10.667 4.776-10.667 10.667v64c0 5.891 4.776 10.667 10.667 10.667h74.667c5.891 0 10.667-4.776 10.667-10.667V256c0-5.891-4.776-10.667-10.667-10.667z" fill="white" />

            <!-- Icono de persona -->
            <path d="M192 213.333c29.455 0 53.333-23.878 53.333-53.333S221.455 106.667 192 106.667c-29.455 0-53.333 23.878-53.333 53.333s23.878 53.333 53.333 53.333z" fill="white" />
            <path d="M192 234.667c-35.346 0-106.667 17.673-106.667 53.019v37.648c0 5.891 4.776 10.667 10.667 10.667h192c5.891 0 10.667-4.776 10.667-10.667v-37.648c0-35.346-71.321-53.019-106.667-53.019z" fill="white" />

            <!-- Definición del gradiente -->
            <defs>
                <linearGradient id="logoGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#3B82F6;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#4F46E5;stop-opacity:1" />
                </linearGradient>
            </defs>
        </svg>

        <!-- Texto del logo -->
        <text x="110" y="42" font-family="Arial, sans-serif" font-size="38" font-weight="bold" fill="url(#textGrad)">
            Plan de Soporte
        </text>
        <text x="110" y="70" font-family="Arial, sans-serif" font-size="20" fill="#6B7280">
            Individualizado
        </text>
    </g>

    <!-- Definición del gradiente para el texto -->
    <defs>
        <linearGradient id="textGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#3B82F6;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#4F46E5;stop-opacity:1" />
        </linearGradient>
    </defs>
</svg>
