import React from 'react';
import { cn } from '@/lib/utils';
import { useState, useEffect } from 'react';
import { Link } from '@inertiajs/react';

interface FooterData {
    wosp_link?: string;
    registration_hours: Array<{
        day: string;
        hours: string;
    }>;
    links: Array<{
        name: string;
        url: string;
        external: boolean;
    }>;
}

interface FooterProps extends React.HTMLAttributes<HTMLElement> {}

const currentYear = new Date().getFullYear();

export default function Footer({ className, ...props }: FooterProps) {

    const [footerData, setFooterData] = useState<FooterData | null>(null);

    useEffect(() => {
        fetch('/api/footer')
            .then(response => response.json())
            .then(data => setFooterData(data))
            .catch(error => console.error('Error fetching footer data:', error));
    }, []);

    return (
        <footer
            className={cn(
                'w-full border-t border-border/60 bg-black relative bottom-0 left-0',
                className
            )}
            {...props}
        >
            <div className='w-full max-w-screen-lg m-auto py-8 px-4 flex text-gray-100 dark:text-gray-100 contrast:text-foreground text-lg flex flex-col md:flex-row md:justify-between md:items-start'>
                <div className="flex flex-col items-start md:items-start">
                    <div className="font-semibold mb-1">Pomocne odnośniki</div>
                    {footerData?.links?.map((link, index) => link.external ? (
                        <a
                            key={index}
                            href={link.url}
                            target="_blank"
                            rel="noopener noreferrer"
                            title={link.name}
                            className="hover:underline"
                        >
                            {link.name}
                        </a>
                    ) : (
                        <Link
                            key={index}
                            href={link.url}
                            title={link.name}
                            className="hover:underline"
                        >
                            {link.name}
                        </Link>
                    ))}
                </div>
                <div className="flex flex-col items-start md:items-start mt-8 mb-8 md:mt-0 md:mb-0">
                    <div className="font-semibold mb-1">Godziny rejestracji</div>
                    <div className="flex flex-row">
                        <ul className='pr-4'>
                            {footerData?.registration_hours?.map((registration_hour, index) => (
                                <li key={index}>{registration_hour.day}</li>
                            ))}
                        </ul>
                        <ul>
                            {footerData?.registration_hours?.map((registration_hour, index) => (
                                <li key={index}>{registration_hour.hours}</li>
                            ))}
                        </ul>
                    </div>
                </div>
                <div className="flex flex-col items-center">
                    <Link href={footerData?.wosp_link || '#'} target="_blank" rel="noopener noreferrer">
                        <img src="/storage/wosp.webp" alt="Zdjęcie przedstawia naklejkę Wielkiej Orkiestry Świątecznej Pomocy, po kliknięciu otworzy się artykuł o wsparciu jakie otrzymaliśmy." title='Kliknij aby przejść do artykułu.' className="w-48 h-auto rounded hover:scale-105 transition-transform select-none" />
                    </Link>
                </div>

            </div>
            <div className="w-full text-center py-4 text-md opacity-70 text-gray-100 dark:text-gray-100 contrast:text-foreground select-none">
                <div>&copy; {currentYear} Samodzielny Publiczny Zakład Opieki Zdrowotnej w Obornikach. Wszelkie prawa zastrzeżone.<br />Wykonane przez <a href='https://bwitek.dev' className="font-semibold hover:underline" target="_blank" rel="noopener noreferrer">Bogusław Witek</a>. Utrzymywane przez <a href='https://e-tmk.com' className="font-semibold hover:underline" target="_blank" rel="noopener noreferrer">TMK</a>.</div>
            </div>
        </footer>
    );
}
