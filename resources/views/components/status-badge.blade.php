{{-- components/status-badge.blade.php (Bootstrap version) --}}
@php
$classes=[
  'submitted'=>'bx-submitted','under_review'=>'bx-under_review',
  'revision_required'=>'bx-revision_required','accepted'=>'bx-accepted',
  'rejected'=>'bx-rejected','waiting_payment'=>'bx-waiting_payment',
  'payment_uploaded'=>'bx-payment_uploaded','payment_verification'=>'bx-payment_verification',
  'paid'=>'bx-paid','published'=>'bx-published',
  'pending'=>'bx-pending','uploaded'=>'bx-uploaded','verified'=>'bx-verified',
];
$cls=$classes[$status]??'bx-gray';
@endphp
<span class="bx {{ $cls }}">{{ $label }}</span>
