import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import HomeCarousel from '@/components/home-carousel/home-carousel';

interface HomepageData {
    title?: string;
    content?: string;
    photo?: string;
}

export default function Home() {
    const [homepageData, setHomepageData] = useState<HomepageData | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setLoading(true);
        fetch('/api/homepage')
            .then(response => response.json())
            .then(data => {
                setHomepageData(data);
                setLoading(false);
            })
            .catch(error => {
                console.error('Error fetching homepage data:', error);
                setLoading(false);
            });
    }, []);

    return (
        <AppLayout>
            <Head title="Strona główna" />
            <HomeCarousel />
            <div>
                <main className="bg-background max-w-screen-xl mx-auto px-4 xl:px-0 pb-12">
                    {loading ? (
                        <div className="flex justify-center items-center h-64">
                            <div className="text-lg">Wczytywanie...</div>
                        </div>
                    ) : (
                        <article>
                            <div className="flex flex-col md:flex-row gap-8">
                                <div className="md:w-1/2">
                                    <h1 className="text-3xl font-bold text-black dark:text-white contrast:text-foreground mb-6">{homepageData?.title}</h1>
                                    <div className="text-lg content" dangerouslySetInnerHTML={{ __html: homepageData?.content || '' }}></div>
                                </div>
                                
                                {homepageData?.photo && (
                                    <div className="md:w-1/2 md:sticky md:top-8">
                                        <img 
                                            className="w-full rounded-lg shadow-md" 
                                            src={homepageData.photo} 
                                            alt="Zdjęcie przedstawiające szpital" 
                                        />
                                    </div>
                                )}
                            </div>
                        </article>
                    )}
                </main>
            </div>
        </AppLayout>
    );
}
