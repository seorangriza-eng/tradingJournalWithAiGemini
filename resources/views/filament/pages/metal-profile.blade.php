<x-filament-panels::page>

<div class="custom-table-container">
    <!-- Judul Header -->
    <div class="custom-table-title">
        <span>Rantai Paling Sering</span>
    </div>

    <!-- Tabel -->
    <div class="custom-table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Asia &rarr; London &rarr; New York</th>
                    <th>Persen</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-light">Reversal &rarr; Continuation &rarr; Continuation</td>
                    <td class="font-bold text-highlight">21.6%</td>
                </tr>
                <tr>
                    <td>Reversal &rarr; Continuation &rarr; Consolidation</td>
                    <td>18.7%</td>
                </tr>
                <tr>
                    <td>Reversal &rarr; Consolidation &rarr; Continuation</td>
                    <td>18.1%</td>
                </tr>
                <tr>
                    <td>Reversal &rarr; Consolidation &rarr; Consolidation</td>
                    <td>15.2%</td>
                </tr>
                <tr>
                    <td>Consolidation &rarr; Consolidation &rarr; Reversal</td>
                    <td>8.2%</td>
                </tr>
                <tr>
                    <td>Consolidation &rarr; Reversal &rarr; Continuation</td>
                    <td>6.4%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="custom-table-container">
    <!-- Judul Header -->
    <div class="custom-table-title">
        <span>Asia</span>
        <span class="arrow">&rarr;</span>
        <span>London</span>
    </div>

    <!-- Tabel -->
    <div class="custom-table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Asia</th>
                    <th>London</th>
                    <th class="text-right">Peluang</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-light">Reversal</td>
                    <td class="text-light">Continuation</td>
                    <td class="text-right font-bold text-highlight">54%</td>
                </tr>
                <tr>
                    <td class="text-light">Reversal</td>
                    <td class="text-muted">Consolidation</td>
                    <td class="text-right">46%</td>
                </tr>
                <tr>
                    <td class="text-muted">Consolidation</td>
                    <td class="text-muted">Consolidation</td>
                    <td class="text-right">43%</td>
                </tr>
                <tr>
                    <td class="text-muted">Consolidation</td>
                    <td class="text-light">Reversal</td>
                    <td class="text-right">38%</td>
                </tr>
                <tr>
                    <td class="text-muted">Consolidation</td>
                    <td class="text-muted">Counter</td>
                    <td class="text-right">12%</td>
                </tr>
                <tr>
                    <td class="text-muted">Consolidation</td>
                    <td class="text-light">Continuation</td>
                    <td class="text-right">7%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="custom-table-container">
    <!-- Judul Header -->
    <div class="custom-table-title">
        <span>London</span>
        <span class="arrow">&rarr;</span>
        <span>New York</span>
    </div>

    <!-- Tabel -->
    <div class="custom-table-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>London</th>
                    <th>New York</th>
                    <th class="text-right">Peluang</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-light">Reversal</td>
                    <td class="text-light">Continuation</td>
                    <td class="text-right font-bold text-highlight">69%</td>
                </tr>
                <tr>
                    <td class="text-light">Continuation</td>
                    <td class="text-light">Continuation</td>
                    <td class="text-right">51%</td>
                </tr>
                <tr>
                    <td class="text-light">Continuation</td>
                    <td class="text-muted">Consolidation</td>
                    <td class="text-right">45%</td>
                </tr>
                <tr>
                    <td class="text-muted">Consolidation</td>
                    <td class="text-light">Continuation</td>
                    <td class="text-right">42%</td>
                </tr>
                <tr>
                    <td class="text-muted">Consolidation</td>
                    <td class="text-muted">Consolidation</td>
                    <td class="text-right">38%</td>
                </tr>
                <tr>
                    <td class="text-muted">Counter</td>
                    <td class="text-light">Reversal</td>
                    <td class="text-right font-bold text-highlight">80%</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Blok Kode CSS Murni -->
<style>
    .custom-table-container {
        border-radius: 12px;
        border: 1px solid #1f2937; /* abu-abu gelap border */
        background-color: #0d0d0d; /* hitam pekat latar belakang */
        padding: 10px;
        color: #e5e7eb;
        font-family: ui-sans-serif, system-ui, sans-serif;
    }

    .custom-table-title {
        margin-bottom: 16px;
        font-size: 18px;
        font-weight: 500;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .custom-table-title .arrow {
        color: #6b7280;
    }

    .custom-table-wrapper {
        overflow-x: auto;
    }

    .custom-table {
        width: 100%;
        text-align: left;
        border-collapse: collapse; /* */
    }

    .custom-table thead tr {
        border-bottom: 1px solid #1f2937;
    }

    .custom-table th {
        padding-bottom: 6px;
        font-size: 14px;
        font-weight: 400;
        color: #9ca3af;
    }

    .custom-table tbody tr {
        border-bottom: 1px solid rgba(31, 41, 55, 0.6);
        transition: background-color 0.2s;
    }

    .custom-table tbody tr:hover {
        background-color: rgba(31, 41, 55, 0.4);
    }

    .custom-table td {
        padding-top: 6px;
        padding-bottom: 6px;
        font-size: 14px;
    }

    /* Utilitas Teks */
    .text-light { color: #ffffff; font-weight: bold;}
    .text-muted { color: #9ca3af; }
    .text-highlight { color: #ffffff; }
    .text-right { text-align: right; } /* */
    .font-bold { font-weight: 700; }
</style>

</x-filament-panels::page>