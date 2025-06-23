@props(['status'])

<span {{ $attributes->class([
    'px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize',
    'bg-green-500/20 text-green-400' => $status === 'paid',
    'bg-yellow-500/20 text-yellow-400' => $status === 'pending',
    'bg-red-500/20 text-red-400' => in_array($status, ['cancelled', 'expired', 'failed']),
    'bg-gray-500/20 text-gray-400' => !in_array($status, ['paid', 'pending', 'cancelled', 'expired', 'failed']),
]) }}>
    @switch($status)
        @case('paid') Pago @break
        @case('pending') Pendente @break
        @case('expired') Expirado @break
        @case('failed') Falhou @break
        @case('cancelled') Cancelado @break
        @default {{ ucfirst($status) }}
    @endswitch
</span>
