import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Download } from 'lucide-react';

const getBreadcrumbs = (categoryTitle: string | null): BreadcrumbItem[] => {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Strona Główna',
            href: '/',
        }
    ];

    if (categoryTitle) {
        breadcrumbs.push({
            title: categoryTitle,
            href: '#',
            disabled: true
        });
    }

    breadcrumbs.push({
        title: 'Cennik badań diagnostycznych',
        href: '/cennik-badan',
    });

    return breadcrumbs;
};

interface PriceData {
    id: number;
    attachment_id: number;
    created_at: string;
    updated_at: string;
}

interface AttachmentData {
    id: number;
    file_name: string;
    file_path: string;
    created_at: string;
    updated_at: string;
}



interface PriceApiResponse {
    price: PriceData;
    attachment: AttachmentData;
    category: string;
}

export default function Price() {
    const [priceData, setPriceData] = useState<PriceApiResponse | null>(null);
    const [loading, setLoading] = useState(true);
    const [categoryTitle, setCategoryTitle] = useState<string | null>(null);

    useEffect(() => {
        setLoading(true);
        fetch('/api/price')
            .then(response => response.json())
            .then(data => {
                setPriceData(data);
                if (data.category) {
                    setCategoryTitle(data.category);
                }
                setLoading(false);
            })
            .catch(error => {
                console.error('Error fetching price data:', error);
                setLoading(false);
            });
    }, []);

    const handleDownload = () => {
        if (priceData?.attachment?.file_path) {
            const link = document.createElement('a');
            link.href = priceData.attachment.file_path;
            link.download = priceData.attachment.file_name || 'cennik.pdf';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    };

    return (
        <AppLayout breadcrumbs={getBreadcrumbs(categoryTitle)}>
            <Head title="Cennik badań diagnostycznych" />
            <div>
                <main className="bg-background max-w-screen-lg mx-auto px-4 xl:px-0 pt-8 pb-12">
                    <div className="flex justify-between items-center mb-6">
                        <h1 className="text-3xl font-bold">Cennik badań diagnostycznych</h1>
                        {priceData?.attachment && (
                            <Button 
                                onClick={handleDownload} 
                                className="flex items-center gap-2"
                            >
                                <Download className="h-4 w-4" />
                                Pobierz PDF
                            </Button>
                        )}
                    </div>

                    {loading ? (
                        <div className="flex justify-center items-center h-64">
                            <div className="text-lg">Wczytywanie...</div>
                        </div>
                    ) : !priceData?.attachment ? (
                        <div className="flex justify-center items-center h-64">
                            <div className="text-lg">Cennik nie jest obecnie dostępny.</div>
                        </div>
                    ) : (
                        <div className="bg-white rounded-lg shadow-md overflow-hidden">
                            <object
                                data={priceData.attachment.file_path}
                                type="application/pdf"
                                className="w-full h-[800px]"
                            >
                                <div className="flex flex-col items-center justify-center p-8 text-center">
                                    <p className="mb-4">Twoja przeglądarka nie obsługuje bezpośredniego wyświetlania plików PDF.</p>
                                    <Button onClick={handleDownload}>Pobierz PDF</Button>
                                </div>
                            </object>
                        </div>
                    )}
                </main>
            </div>
        </AppLayout>
    );
}