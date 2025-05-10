import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app-layout';
import { Phone, Mail } from 'lucide-react';
import { Link } from '@inertiajs/react';
import { SingleImageLightbox, ImageLightbox } from '@/components/image-lightbox';
import AttachmentsList from '@/components/attachments-list';

const truncateText = (text: string, maxLength: number): string => {
    if (text.length <= maxLength) return text;
    return text.slice(0, maxLength) + '…';
};

const getBreadcrumbs = (section: string, slug: string): BreadcrumbItem[] => [
    {
        title: 'Strona Główna',
        href: '/',
    },
    {
        title: 'Aktualności',
        href: '/aktualnosci',
    },
    {
        title: truncateText(section, 35),
        href: `/aktualnosci/${slug}`,
    },
];

interface Photo {
    id: number;
    image_name: string;
    image_desc: string;
    image_path: string;
    created_at: string;
    updated_at: string;
}

interface Attachment {
    id: number;
    file_name: string;
    file_path: string;
    created_at: string;
    updated_at: string;
}

interface News {
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
    photos?: Photo[];
    attachments?: Attachment[];
}

interface NewsApiResponse {
    articleNews: Record<string, News>;
}

interface NewsPageProps {
    slug: string;
}

export default function NewsSlug(props: NewsPageProps) {
    const { slug } = props;
    const [news, setNews] = useState<NewsApiResponse | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        setLoading(true);
        fetch(`/api/news?slug=${encodeURIComponent(slug)}`)
            .then(response => response.json())
            .then(data => {
                setNews(data);
                setLoading(false);
            })
            .catch(error => {
                setNews(null);
                setLoading(false);
                console.error('Error fetching news:', error);
            });
    }, [slug]);

    const sectionName = news && Object.values(news.articleNews)[0]?.title || slug.replace(/-/g, ' ');

    return (
        <AppLayout breadcrumbs={getBreadcrumbs(sectionName, slug)}>
            <Head title={`${sectionName ? `${truncateText(sectionName, 35)}` : ''}`}/>
            <div>
                <main className="bg-background max-w-screen-lg mx-auto px-4 xl:px-0 pt-8 pb-12 min-h-[75vh]">
                    {loading ? (
                        <div className="flex justify-center items-center h-64">
                            <div className="text-lg">Wczytywanie zawartości...</div>
                        </div>
                    ) : !news || Object.keys(news.articleNews).length === 0 ? (
                        <div className="flex justify-center items-center h-64">
                            <div className="text-lg">Brak artykułów.</div>
                        </div>
                    ) : (
                        <div>
                            {Object.values(news.articleNews).map((article) => (
                                <div key={article.id} className="mb-8">
                                    {article.thumbnail ? (
                                        <div className="flex flex-col md:flex-row gap-8">
                                            <div className="md:w-1/2">
                                                <h1 className="text-3xl font-bold mb-4">{article.title}</h1>
                                                
                                                <div className="mb-4 text-foreground">Data publikacji: {new Date(article.published_at).toLocaleDateString('pl-PL')}</div>
                                                
                                                <div className="text-foreground content" dangerouslySetInnerHTML={{ __html: article.body }}></div>
                                            </div>
                                            
                                            <div className="md:w-1/2 md:sticky md:top-8">
                                                <SingleImageLightbox image={{ image_path: article.thumbnail, image_desc: article.title }} />
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="max-w-3xl mx-auto">
                                            <h1 className="text-3xl font-bold mb-4">{article.title}</h1>
                                            
                                            <div className="mb-4 text-foreground">Data publikacji: {new Date(article.published_at).toLocaleDateString('pl-PL')}</div>
                                            
                                            <div className="text-foreground content" dangerouslySetInnerHTML={{ __html: article.body }}></div>
                                        </div>
                                    )}
                                    
                                    {article.photos && article.photos.length > 0 && (
                                        <div className={`mt-8 ${!article.thumbnail ? 'max-w-4xl mx-auto' : ''}`}>
                                            <h2 className={`text-2xl font-semibold mb-4 ${!article.thumbnail ? 'text-center' : ''}`}>Galeria</h2>
                                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                {article.photos.map((photo, index) => (
                                                    <ImageLightbox 
                                                        key={photo.id} 
                                                        images={article.photos as Array<{
                                                            id: number;
                                                            image_path: string;
                                                            image_desc?: string;
                                                        }>} 
                                                        initialIndex={index} 
                                                    />
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                    
                                    <div className="max-w-3xl mx-auto mt-8">
                                        <AttachmentsList attachments={article.attachments || []} />
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}
                </main>
            </div>
        </AppLayout>
    );
}