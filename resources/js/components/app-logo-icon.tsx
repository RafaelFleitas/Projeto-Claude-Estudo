import type { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg {...props} viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" fill="none">
            {/* Center hexagon */}
            <polygon
                points="20,6 29,11 29,21 20,26 11,21 11,11"
                fill="url(#hexGrad)"
                stroke="none"
            />
            {/* Code symbol */}
            <text
                x="20"
                y="19"
                textAnchor="middle"
                dominantBaseline="middle"
                fontSize="7"
                fontWeight="bold"
                fontFamily="monospace"
                fill="white"
            >
                {'</>'}
            </text>
            {/* Top-right node */}
            <circle cx="34" cy="6" r="3" fill="#38bdf8" />
            <line x1="29" y1="11" x2="34" y2="6" stroke="#38bdf8" strokeWidth="1.5" />
            {/* Bottom-right node */}
            <circle cx="36" cy="28" r="2.5" fill="#34d399" />
            <line x1="29" y1="21" x2="36" y2="28" stroke="#34d399" strokeWidth="1.5" />
            {/* Bottom node */}
            <circle cx="20" cy="37" r="2.5" fill="#34d399" />
            <line x1="20" y1="26" x2="20" y2="37" stroke="#34d399" strokeWidth="1.5" />
            {/* Top-left node */}
            <circle cx="6" cy="6" r="3" fill="#38bdf8" />
            <line x1="11" y1="11" x2="6" y2="6" stroke="#38bdf8" strokeWidth="1.5" />
            {/* Left node */}
            <circle cx="3" cy="20" r="2.5" fill="#818cf8" />
            <line x1="11" y1="16" x2="3" y2="20" stroke="#818cf8" strokeWidth="1.5" />
            <defs>
                <linearGradient id="hexGrad" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stopColor="#1e40af" />
                    <stop offset="100%" stopColor="#0891b2" />
                </linearGradient>
            </defs>
        </svg>
    );
}
