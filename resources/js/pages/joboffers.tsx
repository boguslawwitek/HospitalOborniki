import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app-layout';
import AttachmentsList from '@/components/attachments-list';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Strona Główna',
        href: '/',
    },
    {
        title: 'Oferty pracy',
        href: '/oferty-pracy',
    },
];

interface Attachment {
    id: number;
    file_name: string;
    file_path: string;
    file_size: number;
    file_type: string;
    created_at: string;
    updated_at: string;
}

interface Photo {
    id: number;
    image_name: string;
    image_path: string;
    image_size: number;
    image_type: string;
    created_at: string;
    updated_at: string;
}

interface JobOffer {
    id: number;
    title: string;
    slug: string;
    thumbnail: string;
    body: string;
    active: boolean;
    published_at: string;
    created_at: string;
    updated_at: string;
    user_id: number;
    attachments?: Attachment[];
    photos?: Photo[];
}

interface JobOffersApiResponse {
    jobOffers: JobOffer[];
}

export default function JobOffers() {
    const [jobOffers, setJobOffers] = useState<JobOffersApiResponse | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setLoading(true);
        fetch(`/api/joboffers`)
            .then(response => response.json())
            .then(data => {
                setJobOffers(data);
                setLoading(false);
            })
            .catch(error => {
                setJobOffers(null);
                setLoading(false);
                console.error('Error fetching job offers data:', error);
            });
    }, []);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Oferty pracy"/>
            <div>
                <main className="bg-background max-w-screen-lg mx-auto px-4 xl:px-0 pt-8 pb-12">
                    <h1 className="text-3xl font-bold mb-6">Oferty pracy</h1>
                    {loading ? (
                        <div className="flex justify-center items-center h-64">
                            <div className="text-lg">Wczytywanie...</div>
                        </div>
                    ) : !jobOffers || jobOffers.jobOffers.length === 0 ? (
                        <div className="flex justify-center items-center h-64">
                            <div className="text-lg">Brak ofert pracy.</div>
                        </div>
                    ) : (
                        <div>
                            <div className="space-y-8 mb-8">
                                {jobOffers.jobOffers.map((jobOffer) => {
                                    return (
                                        <div key={jobOffer.id} className="p-6 bg-card rounded-lg shadow-sm">
                                            <h2 className="text-2xl font-bold mb-2">{jobOffer.title}</h2>
                                            <div className="mb-2 text-muted-foreground">Data publikacji: {new Date(jobOffer.published_at).toLocaleDateString('pl-PL')}</div>
                                            <div className="text-foreground mb-4 content" dangerouslySetInnerHTML={{ __html: jobOffer.body }}></div>
                                            
                                            <AttachmentsList 
                                                attachments={jobOffer.attachments || []} 
                                                title="Załączniki" 
                                            />
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}
                </main>
            </div>
        </AppLayout>
    );
}
