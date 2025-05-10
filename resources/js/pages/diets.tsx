import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Pagination } from '@/components/pagination';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Calendar, FileText, Download } from 'lucide-react';
import { Skeleton } from '@/components/ui/skeleton';
import { SingleImageLightbox } from '@/components/image-lightbox';

const globalStyles = `
  .diet-photo-container img {
    height: 100% !important;
    object-fit: cover !important;
    width: 100% !important;
  }
`;
import { type BreadcrumbItem } from '@/types';

interface Diet {
  id: number;
  name: string;
  breakfast_photo: string | null;
  lunch_photo: string | null;
  breakfast_body: string | null;
  lunch_body: string | null;
  diet_attachment: string | null;
  published_at: string;
}

interface PaginationData {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

interface DietsData {
  diets: Diet[];
  pagination: PaginationData;
}

interface DietsProps {
  page?: string | number;
}

const getBreadcrumbs = (): BreadcrumbItem[] => [
  {
    title: 'Strona Główna',
    href: '/',
  },
  {
    title: 'Pilotaż "Dobry posiłek"',
    href: '/diety',
  },
];

export default function Diets({ page = 1 }: DietsProps) {
  const [dietsData, setDietsData] = useState<DietsData | null>(null);
  const [loading, setLoading] = useState(true);
  const [currentPage, setCurrentPage] = useState(typeof page === 'string' ? parseInt(page) : page);

  const fetchDiets = (page: number) => {
    setLoading(true);
    fetch(`/api/diets?page=${page}&per_page=7`)
      .then(response => response.json())
      .then(data => {
        setDietsData(data);
        setLoading(false);
      })
      .catch(error => {
        console.error('Błąd podczas pobierania diet:', error);
        setLoading(false);
      });
  };

  useEffect(() => {
    fetchDiets(currentPage);
  }, [currentPage]);
  
  useEffect(() => {
    const initialPage = typeof page === 'string' ? parseInt(page) : page;
    if (initialPage !== currentPage) {
      setCurrentPage(initialPage);
    }
  }, [page]);

  const handlePageChange = (page: number) => {
    setCurrentPage(page);
    window.scrollTo(0, 0);
    
    if (page === 1) {
      window.history.pushState({}, '', '/diety');
    } else {
      window.history.pushState({}, '', `/diety?strona=${page}`);
    }
  };

  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('pl-PL', { day: 'numeric', month: 'long', year: 'numeric' });
  };

  return (
    <AppLayout breadcrumbs={getBreadcrumbs()}>
      <style dangerouslySetInnerHTML={{ __html: globalStyles }} />
      <Head title='Pilotaż "Dobry posiłek"' />
      
      <div className="container mx-auto px-4 py-8 max-w-4xl">
        <h1 className="text-3xl font-bold mb-6">Pilotaż "Dobry posiłek"</h1>
        
        <div className="mb-8 p-6 rounded-lg">
          <p className="mb-4">
            Samodzielny Publiczny Zakład Opieki Zdrowotnej w Obornikach dołączył do programu pilotażowego „Dobry posiłek w szpitalu", który ma celu poprawę żywienia w szpitalach poprzez zwiększenie dostępności porad żywieniowych oraz wdrożenie optymalnego modelu żywienia Pacjentów. <a href="https://isap.sejm.gov.pl/isap.nsf/DocDetails.xsp?id=WDU20230002021" target="_blank" className="text-primary hover:underline">Kliknij tutaj aby przejść do Rozporządzenia Ministra Zdrowia z dnia 25 września 2023 r. w sprawie programu pilotażowego w zakresie edukacji żywieniowej oraz poprawy jakości żywienia w szpitalach – „Dobry posiłek w szpitalu"</a>
          </p>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
            <div className='content'>
              <h3 className="font-semibold mb-2">Godziny wydawania posiłków:</h3>
              <ul className="list-disc pl-5 space-y-1">
                <li>Śniadanie 8:00 - 9:00</li>
                <li>Obiad 13:00 - 14:00</li>
                <li>Kolacja 18:00 - 19:00</li>
                <li>II Kolacja / posiłek nocny 19:00 - 20:00</li>
                <li>Podwieczorek 14:30 - 15:00</li>
              </ul>
            </div>
            
            <div className='content'>
              <h3 className="font-semibold mb-2">Najczęściej stosowane diety:</h3>
              <ul className="list-disc pl-5 space-y-1">
                <li>dieta podstawowa</li>
                <li>dieta łatwostrawna</li>
                <li>dieta z ograniczeniem łatwo przyswajalnych węglowodanów (cukrzycowa)</li>
                <li>dieta papkowata</li>
                <li>dieta bogatobiałkowa</li>
              </ul>

            </div>
          </div>
          <p className="mt-2 text-sm">
                Stosujemy również inne diety, lub ich modyfikacje w zależności od zaleceń lekarza. 
                W ramach programu, na zlecenie lekarza odbywają się również konsultacje dietetyczne.
          </p>
        </div>
        
        {loading ? (
          <div className="space-y-6">
            {[1, 2, 3].map((i) => (
              <Card key={i} className="overflow-hidden">
                <CardHeader>
                  <Skeleton className="h-8 w-3/4" />
                </CardHeader>
                <CardContent>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <Skeleton className="h-64 w-full" />
                      <Skeleton className="h-40 w-full mt-4" />
                    </div>
                    <div>
                      <Skeleton className="h-64 w-full" />
                      <Skeleton className="h-40 w-full mt-4" />
                    </div>
                  </div>
                </CardContent>
                <CardFooter>
                  <Skeleton className="h-10 w-48" />
                </CardFooter>
              </Card>
            ))}
          </div>
        ) : (
          <>
            {dietsData?.diets && dietsData.diets.length > 0 ? (
              <div className="space-y-8">
                {dietsData.diets.map((diet: Diet) => (
                  <Card key={diet.id} className="overflow-hidden">
                    <CardHeader>
                      <CardTitle className="text-2xl">{diet.name}</CardTitle>
                      <div className="flex items-center text-sm text-gray-500">
                        <Calendar className="w-4 h-4 mr-1 dark:text-primary contrast:text-foreground" />
                        <span className="dark:text-primary contrast:text-foreground">{formatDate(diet.published_at)}</span>
                      </div>
                    </CardHeader>
                    <CardContent>
                      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                          <h3 className="text-xl font-semibold mb-3">Śniadanie</h3>
                          
                          {diet.breakfast_photo && (
                            <div className="mb-4">
                              <div className="h-64 overflow-hidden rounded-lg shadow-md diet-photo-container">
                                {diet.breakfast_photo && (
                                  <div className="h-full">
                                    <SingleImageLightbox 
                                      image={{
                                        image_path: diet.breakfast_photo,
                                        image_desc: "Śniadanie: " + diet.name
                                      }}
                                    />
                                  </div>
                                )}
                              </div>
                            </div>
                          )}
                          
                          {diet.breakfast_body && (
                            <div 
                              className="text-sm text-gray-700 mt-3 content text-black dark:text-white contrast:text-foreground"
                              dangerouslySetInnerHTML={{ __html: diet.breakfast_body }} 
                            />
                          )}
                        </div>
                        
                        <div>
                          <h3 className="text-xl font-semibold mb-3">Obiad</h3>
                          
                          {diet.lunch_photo && (
                            <div className="mb-4">
                              <div className="h-64 overflow-hidden rounded-lg shadow-md diet-photo-container">
                                {diet.lunch_photo && (
                                  <div className="h-full">
                                    <SingleImageLightbox 
                                      image={{
                                        image_path: diet.lunch_photo,
                                        image_desc: "Obiad: " + diet.name
                                      }}
                                    />
                                  </div>
                                )}
                              </div>
                            </div>
                          )}
                          
                          {diet.lunch_body && (
                            <div 
                              className="text-sm text-gray-700 mt-3 content text-black dark:text-white contrast:text-foreground"
                              dangerouslySetInnerHTML={{ __html: diet.lunch_body }} 
                            />
                          )}
                        </div>
                      </div>
                    </CardContent>
                    <CardFooter>
                      {diet.diet_attachment && (
                        <a 
                          href={diet.diet_attachment} 
                          target="_blank" 
                          rel="noopener noreferrer"
                          className="inline-flex"
                        >
                          <Button variant="outline" className="flex items-center">
                            <FileText className="w-4 h-4 mr-2" /> 
                            <span className="mr-2">Pobierz jadłospis</span>
                            <Download className="w-4 h-4" />
                          </Button>
                        </a>
                      )}
                    </CardFooter>
                  </Card>
                ))}
                
                {dietsData.pagination && dietsData.pagination.last_page > 1 && (
                  <Pagination 
                    currentPage={dietsData.pagination.current_page} 
                    lastPage={dietsData.pagination.last_page} 
                    onPageChange={handlePageChange}
                    className="my-8"
                  />
                )}
              </div>
            ) : (
              <div className="bg-gray-50 rounded-lg p-8 text-center">
                <p className="text-gray-600">Brak dostępnych diet do wyświetlenia.</p>
              </div>
            )}
          </>
        )}
      </div>
    </AppLayout>
  );
}
