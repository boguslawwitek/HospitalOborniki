import React from 'react';
import FontSizeToggleTab from '../font-size-tabs';
import AppearanceToggleTab from '../appearance-tabs';
import { cn } from '@/lib/utils';
import { useEffect, useState } from 'react';
import { SearchInput } from '@/components/search-input/search-input';
import { Phone } from 'lucide-react';
import { Link } from '@inertiajs/react';

interface HeaderData {
    title1?: string;
    title2?: string;
    subtitle?: string;
    logo?: string;
    telephone: string;
    links: Array<{
        name: string;
        icon?: string;
        'icon-alt'?: string;
        url: string;
        external: boolean;
    }>;
}

interface HeaderProps extends React.HTMLAttributes<HTMLElement> {}

export default function Header({ className, ...props }: HeaderProps) {
    const [headerData, setHeaderData] = useState<HeaderData | null>(null);

    useEffect(() => {
        fetch('/api/header')
            .then(response => response.json())
            .then(data => setHeaderData(data))
            .catch(error => console.error('Error fetching header data:', error));
    }, []);

    const linksMap = headerData?.links && headerData.links.length > 0 ? headerData.links.map((link, index) => link.external ? (
        <a
            key={index}
            href={link.url}
            target="_blank"
            rel="noopener noreferrer"
            title={link.name}
            className="flex items-center gap-2 text-primary hover:underline"
        >
            {link.icon && (
                <img
                    src={`/storage/${link.icon}`}
                    alt={link['icon-alt'] || link.name}
                    className="h-6 object-contain"
                />
            )}
        </a>
    ) : (
        <Link
            key={index}
            href={link.url}
            title={link.name}
            className="flex items-center gap-2 text-primary hover:underline"
        >
            {link.icon && (
                <img
                    src={`/storage/${link.icon}`}
                    alt={link['icon-alt'] || link.name}
                    className="h-6 object-contain"
                />
            )}
        </Link>
    )) : null;

    return (
        <header>
            <section className={cn('flex justify-center items-start p-2 bg-header-background flex-col sm:flex-row sm:justify-between', className)}>
                <div className="flex items-center pb-6 sm:pb-0">
                    <div className="flex flex-row justify-center gap-4 select-none">
                        {linksMap}
                    </div>
                </div>
                <div className="flex flex-col items-left sm:items-end gap-2 lg:flex-row lg:items-center">
                    <FontSizeToggleTab />
                    <AppearanceToggleTab />
                </div>
            </section>
            <section className="bg-header-background">
                <div className='flex flex-col items-start justify-start text-center max-w-screen-xl mx-auto pt-8 pb-8 px-2 md:flex-row md:items-center md:justify-between'>
                    <div className='pb-6 md:pb-0'>
                        <div className='flex flex-row items-center justify-start'>
                            <img
                                src={headerData?.logo}
                                alt="Logo szpitala"
                                className="h-18 w-auto select-none"
                            />
                            <div className="pl-4 pr-4 text-4xl font-semibold text-header-text flex flex-col items-start justify-center">
                                <div className='select-none'>{headerData?.title1}<span className="text-accent">{headerData?.title2}</span></div>
                                <div className="text-lg text-header-text pt-1 text-left md:text-center">{headerData?.subtitle}</div>
                            </div>
                        </div>
                    </div>
                    <div className="w-full sm:w-auto flex flex-col items-center justify-center xl:flex-row xl:items-center xl:justify-center xl:gap-8">
                        <a href={`tel:+48${headerData?.telephone.replaceAll(' ', '')}`} className="text-header-text text-2xl pb-2 font-bold flex items-center gap-2 justify-start">
                            <Phone className="h-6 w-6 mr-1" />{headerData?.telephone}
                        </a>
                        <div className="w-[250px]">
                            <SearchInput className="h-[40px]" />
                        </div>
                    </div>
                </div>
            </section>
        </header>
    );
}