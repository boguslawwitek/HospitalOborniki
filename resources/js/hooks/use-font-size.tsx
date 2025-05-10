import { useEffect, useState } from 'react';

type FontSize = 'normal' | 'large' | 'larger';

export function useFontSize() {
    const [fontSize, setFontSize] = useState<FontSize>(() => {
        if (typeof window !== 'undefined') {
            const saved = localStorage.getItem('font-size');
            if (saved && ['normal', 'large', 'larger'].includes(saved)) {
                return saved as FontSize;
            }
        }
        return 'normal';
    });

    useEffect(() => {
        const root = window.document.documentElement;
        root.classList.remove('text-normal', 'text-large', 'text-larger');
        root.classList.add(`text-${fontSize}`);
        localStorage.setItem('font-size', fontSize);
    }, [fontSize]);

    return {
        fontSize,
        setFontSize,
    };
}
