@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-3">Transaksi Penjualan (Kasir)</h2>

    <form action="{{ route('tps.add') }}" method="POST">
        @csrf
        <div class="row mb-3">
            <div class="col">
                <select name="produk_id" class="form-control">
                    @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_produk }} - Rp {{ number_format($p->harga_jual) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Tambah</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($cart as $item)
            @php $sub = $item['harga'] * $item['qty']; $total += $sub; @endphp
            <tr>
                <td>{{ $item['nama'] }}</td>
                <td>Rp {{ number_format($item['harga']) }}</td>
                <td>{{ $item['qty'] }}</td>
                <td>Rp {{ number_format($sub) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Total: Rp {{ number_format($total) }}</h3>

    <form action="{{ route('tps.checkout') }}" method="POST" class="mt-3">
        @csrf
        <input type="number" name="bayar" class="form-control mb-2" placeholder="Jumlah Pembayaran" required>
        <button class="btn btn-success">Selesaikan Transaksi</button>
    </form>

    @if(session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
    @endif

</div>
@endsection
