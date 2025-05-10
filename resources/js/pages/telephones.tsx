import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { type BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/app-layout';
import { Phone } from 'lucide-react';

interface TelephoneData {
  name: string;
  number: string;
}

interface TelephoneEntry {
  type: string;
  data: TelephoneData;
}

interface TelephoneItem {
  id: number;
  section: string;
  telephones: TelephoneEntry[];
  sort_order: number;
  created_at: string;
  updated_at: string;
}

interface TelephonesApiResponse {
  telephones: TelephoneItem[];
}

const getBreadcrumbs = (): BreadcrumbItem[] => [
  {
    title: 'Strona Główna',
    href: '/',
  },
  {
    title: 'Telefony',
    href: '/telefony',
  },
];

export default function Telephones() {
  const [telephonesData, setTelephonesData] = useState<TelephonesApiResponse | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(true);
    fetch('/api/telephones')
      .then(response => response.json())
      .then(data => {
        setTelephonesData(data);
        setLoading(false);
      })
      .catch(error => {
        console.error('Error fetching telephones:', error);
        setTelephonesData(null);
        setLoading(false);
      });
  }, []);

  return (
    <AppLayout breadcrumbs={getBreadcrumbs()}>
      <Head title="Telefony" />
      <div>
        <main className="bg-background max-w-screen-lg mx-auto px-4 xl:px-0 pt-8 pb-12 min-h-[75vh]">
          <h1 className="text-3xl font-bold mb-8 text-center">Telefony</h1>
          
          {loading ? (
            <div className="flex justify-center items-center h-64">
              <div className="text-lg">Wczytywanie zawartości...</div>
            </div>
          ) : !telephonesData || !telephonesData.telephones || telephonesData.telephones.length === 0 ? (
            <div className="flex justify-center items-center h-64">
              <div className="text-lg">Brak danych.</div>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
              {telephonesData.telephones.map((item) => (
                <div key={item.id} className="bg-card rounded-lg shadow-sm overflow-hidden">
                  <div className="bg-foreground dark:bg-primary contrast:bg-primary p-4">
                    <h2 className="text-xl font-semibold text-primary-foreground">{item.section}</h2>
                  </div>
                  <div className="p-6">
                    <ul className="space-y-4">
                      {item.telephones.map((telephone, index) => (
                        <li key={index} className="flex items-center justify-between group">
                          <div className="flex items-center">
                            <Phone className="h-5 w-5 mr-3 text-primary" />
                            <span className="font-medium">{telephone.data.name}</span>
                          </div>
                          <a 
                            href={`tel:${telephone.data.number.replace(/\s+/g, '')}`} 
                            className="text-primary font-semibold hover:underline transition-colors"
                          >
                            {telephone.data.number}
                          </a>
                        </li>
                      ))}
                    </ul>
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
