import { Head } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import AppLayout from '@/layouts/app-layout';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { type BreadcrumbItem } from '@/types';
import { Textarea } from '@/components/ui/textarea';
import { cn } from '@/lib/utils';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Strona główna',
        href: '/',
    },
    {
        title: 'Kontakt',
        href: '/kontakt',
    },
];

interface ContactData {
    system_email: string;
    telephone: string;
    email: string;
    address: string;
    fax: string;
}

interface ContactApiResponse {
    contact: ContactData;
}

export default function Contact() {
    const [contactData, setContactData] = useState<ContactData | null>(null);
    const [loading, setLoading] = useState(true);
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        phone: '',
        subject: '',
        message: '',
        agreement: false,
    });
    const [errors, setErrors] = useState<Record<string, string>>({});
    const [submitStatus, setSubmitStatus] = useState<{
        success: boolean;
        message: string;
    } | null>(null);
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        fetch('/api/contact')
            .then(response => response.json())
            .then((data: ContactApiResponse) => {
                setContactData(data.contact);
                setLoading(false);
            })
            .catch(error => {
                console.error('Error fetching contact data:', error);
                setLoading(false);
            });
    }, []);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value,
        }));

        // Clear error for this field when user starts typing
        if (errors[name]) {
            setErrors(prev => {
                const newErrors = { ...prev };
                delete newErrors[name];
                return newErrors;
            });
        }
    };

    const handleCheckboxChange = (checked: boolean) => {
        setFormData(prev => ({
            ...prev,
            agreement: checked,
        }));

        // Clear error for agreement when user checks it
        if (errors.agreement) {
            setErrors(prev => {
                const newErrors = { ...prev };
                delete newErrors.agreement;
                return newErrors;
            });
        }
    };

    const validateForm = () => {
        const newErrors: Record<string, string> = {};
        
        if (!formData.name.trim()) {
            newErrors.name = 'Imię i nazwisko jest wymagane';
        }
        
        if (!formData.email.trim()) {
            newErrors.email = 'Adres e-mail jest wymagany';
        } else if (!/^\S+@\S+\.\S+$/.test(formData.email)) {
            newErrors.email = 'Podaj poprawny adres e-mail';
        }
        
        if (!formData.subject.trim()) {
            newErrors.subject = 'Temat jest wymagany';
        }
        
        if (!formData.message.trim()) {
            newErrors.message = 'Treść wiadomości jest wymagana';
        }
        
        if (!formData.agreement) {
            newErrors.agreement = 'Wymagana jest zgoda na przetwarzanie danych osobowych';
        }
        
        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }
        
        setSubmitting(true);
        setSubmitStatus(null);
        
        try {
            const csrfToken = document.head?.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            const response = await fetch('/kontakt/wyslij', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (response.ok) {
                setSubmitStatus({
                    success: true,
                    message: data.message || 'Wiadomość została wysłana pomyślnie.'
                });
                
                // Resetowanie formularza po udanym wysłaniu
                setFormData({
                    name: '',
                    email: '',
                    phone: '',
                    subject: '',
                    message: '',
                    agreement: false,
                });
            } else {
                setSubmitStatus({
                    success: false,
                    message: data.message || 'Wystąpił błąd podczas wysyłania wiadomości.'
                });
                
                if (data.errors) {
                    setErrors(data.errors);
                }
            }
        } catch (error) {
            console.error('Błąd wysyłania formularza:', error);
            setSubmitStatus({
                success: false,
                message: 'Wystąpił błąd podczas wysyłania wiadomości. Spróbuj ponownie później.'
            });
        } finally {
            setSubmitting(false);
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Kontakt" />
            <div className="bg-background">
                <main className="max-w-screen-xl mx-auto px-4 xl:px-0 py-8">
                    <h1 className="text-3xl font-bold mb-6">Kontakt</h1>
                    
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <Card>
                                <CardHeader>
                                    <CardTitle>Dane kontaktowe</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    {loading ? (
                                        <div className="animate-pulse space-y-3">
                                            <div className="h-4 bg-muted rounded w-3/4"></div>
                                            <div className="h-4 bg-muted rounded w-1/2"></div>
                                            <div className="h-4 bg-muted rounded w-2/3"></div>
                                            <div className="h-4 bg-muted rounded w-3/5"></div>
                                            <div className="h-4 bg-muted rounded w-1/3"></div>
                                        </div>
                                    ) : contactData ? (
                                        <>
                                            <div>
                                                <h3 className="font-medium text-muted-foreground">Adres:</h3>
                                                <p className="mt-1" dangerouslySetInnerHTML={{ __html: contactData.address }}></p>
                                            </div>
                                            
                                            <div>
                                                <h3 className="font-medium text-muted-foreground">Telefon:</h3>
                                                <p className="mt-1">{contactData.telephone}</p>
                                            </div>
                                            
                                            <div>
                                                <h3 className="font-medium text-muted-foreground">Fax:</h3>
                                                <p className="mt-1">{contactData.fax}</p>
                                            </div>
                                            
                                            <div>
                                                <h3 className="font-medium text-muted-foreground">E-mail:</h3>
                                                <p className="mt-1">
                                                    <a 
                                                        href={`mailto:${contactData.email}`} 
                                                        className="text-primary hover:underline"
                                                    >
                                                        {contactData.email}
                                                    </a>
                                                </p>
                                            </div>
                                        </>
                                    ) : (
                                        <p>Nie udało się załadować danych kontaktowych.</p>
                                    )}
                                </CardContent>
                            </Card>
                            
                            <div className="mt-8">
                                <Card>
                                    <CardHeader>
                                        <CardTitle>Mapa</CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        <div className="aspect-video rounded-md overflow-hidden">
                                            <iframe 

                                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2420.3711291827294!2d16.8160558!3d52.6532738!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47046cc587759f69%3A0xe9da2228ba6bf067!2sSamodzielny%20Publiczny%20Zak%C5%82ad%20Opieki%20Zdrowotnej%20w%20Obornikach!5e0!3m2!1spl!2spl!4v1745879921955!5m2!1spl!2spl" 
                                                width="100%" 
                                                height="100%" 
                                                style={{ border: 0 }} 
                                                allowFullScreen 
                                                loading="lazy" 
                                                referrerPolicy="no-referrer-when-downgrade"
                                            ></iframe>
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        </div>
                        
                        <div>
                            <Card>
                                <CardHeader>
                                    <CardTitle>Formularz kontaktowy</CardTitle>
                                    <CardDescription>Skontaktuj się z nami</CardDescription>
                                </CardHeader>
                                <CardContent>
                                    {submitStatus && (
                                        <Alert className={`mb-6 ${submitStatus.success ? 'bg-green-50 text-green-800 border-green-200 contrast:bg-black contrast:border-green-700' : 'bg-red-50 dark:bg-red-200 contrast:bg-black contrast:border-red-700 text-red-800 border-red-200'}`}>
                                            <AlertTitle className='contrast:text-foreground'>{submitStatus.success ? 'Sukces!' : 'Błąd!'}</AlertTitle>
                                            <AlertDescription className="dark:text-black">{submitStatus.message}</AlertDescription>
                                        </Alert>
                                    )}
                                    
                                    <form onSubmit={handleSubmit} className="space-y-4">
                                        <div className="space-y-2">
                                            <Label htmlFor="name">Imię i nazwisko *</Label>
                                            <Input
                                                id="name"
                                                name="name"
                                                autoComplete='off'
                                                value={formData.name}
                                                onChange={handleChange}
                                                className={errors.name ? 'border-red-500' : ''}
                                            />
                                            {errors.name && (
                                                <p className="text-sm text-red-500">{errors.name}</p>
                                            )}
                                        </div>
                                        
                                        <div className="space-y-2">
                                            <Label htmlFor="email">Adres e-mail *</Label>
                                            <Input
                                                id="email"
                                                name="email"
                                                autoComplete='off'
                                                type="email"
                                                value={formData.email}
                                                onChange={handleChange}
                                                className={errors.email ? 'border-red-500' : ''}
                                            />
                                            {errors.email && (
                                                <p className="text-sm text-red-500">{errors.email}</p>
                                            )}
                                        </div>
                                        
                                        <div className="space-y-2">
                                            <Label htmlFor="phone">Telefon</Label>
                                            <Input
                                                autoComplete='off'
                                                id="phone"
                                                name="phone"
                                                value={formData.phone}
                                                onChange={handleChange}
                                            />
                                        </div>
                                        
                                        <div className="space-y-2">
                                            <Label htmlFor="subject">Temat *</Label>
                                            <Input
                                                autoComplete='off'
                                                id="subject"
                                                name="subject"
                                                value={formData.subject}
                                                onChange={handleChange}
                                                className={errors.subject ? 'border-red-500' : ''}
                                            />
                                            {errors.subject && (
                                                <p className="text-sm text-red-500">{errors.subject}</p>
                                            )}
                                        </div>
                                        
                                        <div className="space-y-2">
                                            <Label htmlFor="message">Treść wiadomości *</Label>
                                            <Textarea
                                                autoComplete='off'
                                                id="message"
                                                name="message"
                                                rows={5}
                                                value={formData.message}
                                                onChange={handleChange}
                                                className={cn('dark:bg-transparent', errors.message ? 'border-red-500' : '')}
                                            />
                                            {errors.message && (
                                                <p className="text-sm text-red-500">{errors.message}</p>
                                            )}
                                        </div>
                                        
                                        <div className="flex items-start space-x-2">
                                            <Checkbox
                                                id="agreement"
                                                checked={formData.agreement}
                                                onCheckedChange={handleCheckboxChange}
                                                className={`mt-1 ${errors.agreement ? 'border-red-500' : ''}`}
                                            />
                                            <div className="grid gap-1.5 leading-none">
                                                <Label
                                                    htmlFor="agreement"
                                                    className="text-sm font-normal"
                                                >
                                                    Wyrażam zgodę na przetwarzanie moich danych osobowych podanych w powyższym formularzu. *
                                                </Label>
                                                {errors.agreement && (
                                                    <p className="text-sm text-red-500">{errors.agreement}</p>
                                                )}
                                            </div>
                                        </div> 
                                        <div className="mt-6">
                                            <Button 
                                                type="submit" 
                                                disabled={submitting}
                                                className="cursor-pointer"
                                            >
                                                {submitting ? 'Wysyłanie...' : 'Wyślij wiadomość'}
                                            </Button>
                                        </div>
                                    </form>
                                </CardContent>
                            </Card>
                        </div>
                    </div>
                </main>
            </div>
        </AppLayout>
    );
}
