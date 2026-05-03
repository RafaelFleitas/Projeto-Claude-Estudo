import { Head } from '@inertiajs/react';
import type { Contract, ContractPdf, ContractStatus } from '@/types';

interface Props {
    contract: Contract;
    contractPdf: ContractPdf;
    validationUrl: string;
}

const statusLabels: Record<ContractStatus, string> = {
    pending: 'Pendente',
    active: 'Ativo',
    completed: 'Concluído',
    cancelled: 'Cancelado',
};

export default function ValidateShow({ contract, contractPdf, validationUrl }: Props) {
    return (
        <>
            <Head title="Validação de Documento" />

            <div className="min-h-screen bg-gray-50 flex items-center justify-center p-4">
                <main className="w-full max-w-lg bg-white rounded-xl shadow-sm border p-8 space-y-6">
                    <div className="text-center space-y-3">
                        <div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100">
                            <svg
                                className="w-8 h-8 text-green-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                            >
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h1 className="text-2xl font-bold text-gray-900">Documento Válido</h1>
                        <p className="text-sm text-gray-500">
                            Este documento foi verificado e é autêntico.
                        </p>
                    </div>

                    <section aria-labelledby="contract-heading" className="space-y-3">
                        <h2 id="contract-heading" className="text-sm font-semibold uppercase tracking-wide text-gray-500">
                            Dados do Contrato
                        </h2>
                        <dl className="divide-y divide-gray-100 rounded-lg border">
                            <div className="grid grid-cols-2 px-4 py-3">
                                <dt className="text-sm text-gray-500">Contrato</dt>
                                <dd className="text-sm font-medium text-gray-900">{contract.contrato}</dd>
                            </div>
                            {contract.projeto && (
                                <div className="grid grid-cols-2 px-4 py-3">
                                    <dt className="text-sm text-gray-500">Projeto</dt>
                                    <dd className="text-sm font-medium text-gray-900">{contract.projeto}</dd>
                                </div>
                            )}
                            <div className="grid grid-cols-2 px-4 py-3">
                                <dt className="text-sm text-gray-500">Status</dt>
                                <dd className="text-sm font-medium text-gray-900">{statusLabels[contract.status]}</dd>
                            </div>
                            {contract.valor_total && (
                                <div className="grid grid-cols-2 px-4 py-3">
                                    <dt className="text-sm text-gray-500">Valor Total</dt>
                                    <dd className="text-sm font-medium text-gray-900">{contract.valor_total}</dd>
                                </div>
                            )}
                        </dl>
                    </section>

                    <section aria-labelledby="pdf-heading" className="space-y-3">
                        <h2 id="pdf-heading" className="text-sm font-semibold uppercase tracking-wide text-gray-500">
                            Informações do Documento
                        </h2>
                        <dl className="divide-y divide-gray-100 rounded-lg border">
                            <div className="grid grid-cols-2 px-4 py-3">
                                <dt className="text-sm text-gray-500">Código de validação</dt>
                                <dd className="text-sm font-mono font-medium text-gray-900 break-all">
                                    {contractPdf.validation_code}
                                </dd>
                            </div>
                            <div className="grid grid-cols-2 px-4 py-3">
                                <dt className="text-sm text-gray-500">Gerado em</dt>
                                <dd className="text-sm font-medium text-gray-900">
                                    {new Date(contractPdf.generated_at).toLocaleDateString('pt-BR', {
                                        day: '2-digit', month: '2-digit', year: 'numeric',
                                        hour: '2-digit', minute: '2-digit',
                                    })}
                                </dd>
                            </div>
                            {contractPdf.generated_by_user && (
                                <div className="grid grid-cols-2 px-4 py-3">
                                    <dt className="text-sm text-gray-500">Gerado por</dt>
                                    <dd className="text-sm font-medium text-gray-900">
                                        {contractPdf.generated_by_user.name}
                                    </dd>
                                </div>
                            )}
                        </dl>
                    </section>

                    <section aria-labelledby="url-heading" className="space-y-2">
                        <h2 id="url-heading" className="text-sm font-semibold uppercase tracking-wide text-gray-500">
                            URL de Validação
                        </h2>
                        <p className="text-xs font-mono break-all rounded-lg bg-gray-50 border px-3 py-2 text-gray-600">
                            {validationUrl}
                        </p>
                    </section>
                </main>
            </div>
        </>
    );
}
