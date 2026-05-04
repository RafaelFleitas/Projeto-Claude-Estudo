import { useState } from 'react';
import { Button } from '@/components/ui/button';

interface Props {
    analyzeUrl: string;
    label?: string;
}

export default function AiAnalysisPanel({ analyzeUrl, label = 'Resumir com IA' }: Props) {
    const [loading, setLoading] = useState(false);
    const [analysis, setAnalysis] = useState<string | null>(null);
    const [error, setError] = useState<string | null>(null);

    async function handleAnalyze() {
        setLoading(true);
        setAnalysis(null);
        setError(null);

        try {
            const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content ?? '';

            const res = await fetch(analyzeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({}),
            });

            const data = await res.json();

            if (res.status === 429) {
                setError('Limite de análises atingido. Tente novamente em 1 hora.');
            } else if (!res.ok || data.error) {
                setError(data.error ?? 'Erro ao analisar documento.');
            } else {
                setAnalysis(data.analysis);
            }
        } catch {
            setError('Erro de conexão. Tente novamente.');
        } finally {
            setLoading(false);
        }
    }

    return (
        <div className="space-y-3">
            <Button
                type="button"
                variant="outline"
                onClick={handleAnalyze}
                disabled={loading}
            >
                {loading ? 'Analisando…' : label}
            </Button>

            {error && (
                <div className="rounded-md border border-destructive/50 bg-destructive/10 px-4 py-3 text-sm text-destructive">
                    {error}
                </div>
            )}

            {analysis && (
                <div className="rounded-md border bg-muted/30 px-4 py-4 space-y-2">
                    <p className="text-xs font-medium text-muted-foreground uppercase tracking-wide">Análise da IA</p>
                    <div className="text-sm whitespace-pre-wrap leading-relaxed">{analysis}</div>
                </div>
            )}
        </div>
    );
}
