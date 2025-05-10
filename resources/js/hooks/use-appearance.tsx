import { useCallback, useEffect, useState } from 'react';

export type Appearance = 'light' | 'dark' | 'contrast';

const prefersDark = () => {
    if (typeof window === 'undefined') {
        return false;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches;
};

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;
    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const applyTheme = (appearance: Appearance) => {
    if (appearance === 'contrast') {
        document.documentElement.classList.remove('dark');
        document.documentElement.classList.add('contrast');
    } else if (appearance === 'dark') {
        document.documentElement.classList.remove('contrast');
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('contrast');
        document.documentElement.classList.remove('dark');
    }
};

const mediaQuery = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return window.matchMedia('(prefers-color-scheme: dark)');
};

export function initializeTheme() {
    const savedAppearance = localStorage.getItem('appearance') as Appearance;
    const prefersDark = mediaQuery()?.matches;
    
    // If no saved appearance and user prefers dark, set dark mode
    if (!savedAppearance && prefersDark) {
        applyTheme('dark');
        localStorage.setItem('appearance', 'dark');
    } else {
        applyTheme(savedAppearance || 'light');
    }
}

export function useAppearance() {
    const [appearance, setAppearance] = useState<Appearance>('light');

    const updateAppearance = useCallback((mode: Appearance) => {
        setAppearance(mode);

        // Store in localStorage for client-side persistence...
        localStorage.setItem('appearance', mode);

        // Store in cookie for SSR...
        setCookie('appearance', mode);

        applyTheme(mode);
    }, []);

    useEffect(() => {
        const savedAppearance = localStorage.getItem('appearance') as Appearance | null;
        const prefersDark = mediaQuery()?.matches;
        
        // If no saved appearance and user prefers dark, set dark mode
        if (!savedAppearance && prefersDark) {
            updateAppearance('dark');
        } else {
            updateAppearance(savedAppearance || 'light');
        }
    }, [updateAppearance]);

    return { appearance, updateAppearance } as const;
}
