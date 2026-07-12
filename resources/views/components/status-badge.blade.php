{{--
  Usage: <x-status-badge :status="$article->status" :label="$article->status_label"/>
--}}
@props(['status' => 'draft', 'label' => null])
@php
$map = [
    'submitted'          => ['bg'=>'#EBF8FF','color'=>'#2B6CB0','dot'=>'#2B6CB0'],
    'under_review'       => ['bg'=>'#FEFCE8','color'=>'#C05621','dot'=>'#C05621'],
    'revision_required'  => ['bg'=>'#FFF7ED','color'=>'#C05621','dot'=>'#C05621'],
    'accepted'           => ['bg'=>'#F0FDF4','color'=>'#2F855A','dot'=>'#2F855A'],
    'rejected'           => ['bg'=>'#FEF2F2','color'=>'#C53030','dot'=>'#C53030'],
    'waiting_payment'    => ['bg'=>'#FAF5FF','color'=>'#6B46C1','dot'=>'#6B46C1'],
    'payment_uploaded'   => ['bg'=>'#EBF8FF','color'=>'#2B6CB0','dot'=>'#2B6CB0'],
    'paid'               => ['bg'=>'#F0FDFA','color'=>'#285E61','dot'=>'#285E61'],
    'published'          => ['bg'=>'#F0FDF4','color'=>'#2F855A','dot'=>'#2F855A'],
    'draft'              => ['bg'=>'#F7FAFC','color'=>'#718096','dot'=>'#718096'],
    'pending'            => ['bg'=>'#FEFCE8','color'=>'#C05621','dot'=>'#C05621'],
    'in_progress'        => ['bg'=>'#EBF8FF','color'=>'#2B6CB0','dot'=>'#2B6CB0'],
    'completed'          => ['bg'=>'#F0FDF4','color'=>'#2F855A','dot'=>'#2F855A'],
    'declined'           => ['bg'=>'#FEF2F2','color'=>'#C53030','dot'=>'#C53030'],
    'active'             => ['bg'=>'#F0FDF4','color'=>'#2F855A','dot'=>'#2F855A'],
    'inactive'           => ['bg'=>'#F7FAFC','color'=>'#718096','dot'=>'#718096'],
    'verified'           => ['bg'=>'#F0FDFA','color'=>'#285E61','dot'=>'#285E61'],
    'uploaded'           => ['bg'=>'#EBF8FF','color'=>'#2B6CB0','dot'=>'#2B6CB0'],
];
$style = $map[$status] ?? $map['draft'];
$text  = $label ?? ucfirst(str_replace('_', ' ', $status));
@endphp
<span {{ $attributes }} style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:500;white-space:nowrap;background:{{ $style['bg'] }};color:{{ $style['color'] }};">
    <span style="width:6px;height:6px;border-radius:50%;background:{{ $style['dot'] }};flex-shrink:0;"></span>
    {{ $text }}
</span>
