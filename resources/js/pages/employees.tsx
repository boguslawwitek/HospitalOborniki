import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app-layout';
import { Phone, Mail } from 'lucide-react';
import { Link } from '@inertiajs/react';

const getBreadcrumbs = (section: string, slug: string): BreadcrumbItem[] => [
    {
        title: 'Strona Główna',
        href: '/',
    },
    {
        title: section || slug.replace(/-/g, ' '),
        href: `/administracja/${slug}`,
    },
];

interface Employee {
    name: string;
    slug?: string;
    sort_order?: number;
    employees: Array<{
        type: string;
        data: {
            [key: string]: string | null;
        };
    }>;
}

interface EmployeesApiResponse {
    main: Record<string, Employee>;
    extra: Record<string, Employee>;
}

interface EmployeesPageProps {
    slug: string;
}

export default function Employees(props: EmployeesPageProps) {
    const { slug } = props;
    const [employees, setEmployees] = useState<EmployeesApiResponse | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setLoading(true);
        fetch(`/api/employees?slug=${encodeURIComponent(slug)}`)
            .then(response => response.json())
            .then(data => {
                setEmployees(data);
                setLoading(false);
            })
            .catch(error => {
                setEmployees(null);
                setLoading(false);
                console.error('Error fetching employees:', error);
            });
    }, [slug]);

    const mainSections = employees?.main ? Object.values(employees.main) : [];
    const extraSections = employees?.extra ? Object.values(employees.extra) : [];
    const sectionName = mainSections.length > 0 ? mainSections[0].name : '';

    return (
        <AppLayout breadcrumbs={getBreadcrumbs(sectionName, '')}>
            <Head title={`${sectionName ? `${sectionName}` : ''}`}/>
            <div>
                <main className="bg-background max-w-screen-lg mx-auto px-4 xl:px-0 pt-8 pb-12 min-h-[75vh]">
                    <h1 className="text-3xl font-bold mb-6">{sectionName}</h1>
                    {loading ? (
                        <div>Pobieranie listy pracowników...</div>
                    ) : !employees || (mainSections.length === 0 && extraSections.length === 0) ? (
                        <>
                            <div>Sekcja nie istnieje lub brak pracowników w tej sekcji.</div>
                            <Link className='text-primary font-bold hover:underline' href={route("home")}>Powrót na stronę główną</Link>
                        </>
                    ) : (
                        <>
                            {mainSections
                                .sort((a: any, b: any) => (a.sort_order ?? 0) - (b.sort_order ?? 0))
                                .map((section: any, index: number) => (
                                    <div key={index} className="mb-10">
                                        <ul className="flex flex-col gap-4">
                                            {section.employees.map((person: any, idx: number) => (
                                                <li key={idx} className="bg-page-card dark:bg-page-card shadow-md rounded-lg p-4 flex items-center gap-4 w-full contrast:border contrast:border-foreground/60">
                                                    <div className="flex-shrink-0 w-12 h-12 rounded-full bg-primary text-white dark:text-black contrast:text-black flex items-center justify-center text-xl font-bold uppercase">
                                                        {person.data.first_name?.[0] || ''}{person.data.last_name?.[0] || ''}
                                                    </div>
                                                    <div className="flex flex-col flex-1 min-w-0">
                                                        <div className="font-semibold text-lg text-black dark:text-gray-300 contrast:text-foreground truncate">{person.data.title ? person.data.title + ' ' : ''}{person.data.first_name} {person.data.last_name}</div>
                                                        {person.data.position && <div className="text-sm text-foreground truncate">{person.data.position}</div>}
                                                        <div className="flex flex-wrap gap-4 mt-1">
                                                            {person.data.email && <div className="flex items-center gap-2 text-sm text-foreground font-semibold contrast:text-foreground"><Mail className="h-4 w-4" /> {person.data.email}</div>}
                                                            {person.data.phone && <div className="flex items-center gap-2 text-sm text-foreground font-semibold contrast:text-foreground"><Phone className="h-4 w-4" /> {person.data.phone}</div>}
                                                        </div>
                                                    </div>
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                ))}
                            {extraSections.length > 0 && (
                                <div className="mt-16">
                                    {extraSections
                                        .sort((a: any, b: any) => (a.sort_order ?? 0) - (b.sort_order ?? 0))
                                        .map((section: any) => (
                                            <div key={section.name} className="mb-10">
                                                <h2 className="text-xl font-semibold mb-4">{section.name}</h2>
                                                <ul className="flex flex-col gap-4">
                                                    {section.employees.map((person: any, idx: number) => (
                                                        <li key={idx} className="bg-page-card dark:bg-page-card shadow-md rounded-lg p-4 flex items-center gap-4 w-full contrast:border contrast:border-foreground/60">
                                                            <div className="flex-shrink-0 w-12 h-12 rounded-full bg-primary text-white dark:text-black contrast:text-black flex items-center justify-center text-xl font-bold uppercase">
                                                                {person.data.first_name?.[0] || ''}{person.data.last_name?.[0] || ''}
                                                            </div>
                                                            <div className="flex flex-col flex-1 min-w-0">
                                                                <div className="font-semibold text-lg text-black dark:text-gray-300 contrast:text-foreground truncate">{person.data.title ? person.data.title + ' ' : ''}{person.data.first_name} {person.data.last_name}</div>
                                                                {person.data.position && <div className="text-sm text-foreground truncate">{person.data.position}</div>}
                                                                <div className="flex flex-wrap gap-4 mt-1">
                                                                    {person.data.email && <div className="flex items-center gap-2 text-sm text-foreground font-semibold contrast:text-foreground"><Mail className="h-4 w-4" /> {person.data.email}</div>}
                                                                    {person.data.phone && <div className="flex items-center gap-2 text-sm text-foreground font-semibold contrast:text-foreground"><Phone className="h-4 w-4" /> {person.data.phone}</div>}
                                                                </div>
                                                            </div>
                                                        </li>
                                                    ))}
                                                </ul>
                                            </div>
                                        ))}
                                </div>
                            )}
                        </>
                    )}
                </main>
            </div>
        </AppLayout>
    );
}