import { useFontSize } from '@/hooks/use-font-size';
import { cn } from '@/lib/utils';
import { LucideIcon, Type } from 'lucide-react';
import { HTMLAttributes } from 'react';

export default function FontSizeToggleTab({ className = '', ...props }: HTMLAttributes<HTMLDivElement>) {
    const { fontSize, setFontSize } = useFontSize();

    const tabs: { value: 'normal' | 'large' | 'larger'; icon: LucideIcon; label: string }[] = [
        { value: 'normal', icon: Type, label: 'Normalny' },
        { value: 'large', icon: Type, label: 'Duży' },
        { value: 'larger', icon: Type, label: 'Bardzo duży' },
    ];

    return (
        <div className={cn('inline-flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800 contrast:bg-primary select-none', className)} {...props}>
            {tabs.map(({ value, icon: Icon, label }) => (
                <button
                    key={value}
                    onClick={() => setFontSize(value)}
                    className={cn(
                        'flex items-center rounded-md px-3.5 py-1.5 transition-colors',
                        fontSize === value
                            ? 'bg-white shadow-xs dark:bg-neutral-700 dark:text-neutral-100 contrast:bg-secondary contrast:text-white'
                            : 'text-neutral-500 hover:bg-neutral-200/60 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-700/60 contrast:text-black',
                    )}
                >
                    <Icon className={cn('-ml-1 w-4', {
                        'h-3': value === 'normal',
                        'h-4': value === 'large',
                        'h-5': value === 'larger',
                    })} />
                    <span className="ml-1.5 text-sm">{label}</span>
                </button>
            ))}
        </div>
    );
}
