<x-filament-panels::page>

    <div class="space-y-8">

        {{-- =========================
             FOUNDATION MODEL
        ========================== --}}
        <div>
            <h2 class="text-xl font-bold mb-3">
                Foundation Model
            </h2>

            <div class="tutorial-table">

                {{-- Header --}}
                <div class="tutorial-row tutorial-header">
                    <div>Nama Tutorial</div>
                    <div class="text-right">Aksi</div>
                </div>

                {{-- Data --}}
                @foreach (collect(Storage::disk('public')->files('tutorials/foundation-model'))->sort() as $video)
                    <div class="tutorial-row">

                        <div class="font-medium text-sm">
                            {{ pathinfo($video, PATHINFO_FILENAME) }}
                        </div>

                        <div class="text-right">
                            <a
                                href="{{ Storage::disk('public')->url($video) }}"
                                target="_blank"
                            >
                                ▶ Tonton
                            </a>
                        </div>

                    </div>
                @endforeach

            </div>
        </div>


        {{-- =========================
             EXPANSION MODEL
        ========================== --}}
        <div>
            <h2 class="text-xl font-bold mb-3 expansion-title">
                Expansion Model
            </h2>

            <div class="tutorial-table">

                {{-- Header --}}
                <div class="tutorial-row tutorial-header">
                    <div>Nama Tutorial</div>
                    <div class="text-right">Aksi</div>
                </div>

                {{-- Data --}}
                @foreach (collect(Storage::disk('public')->files('tutorials/expansion-model'))->sort() as $video)
                    <div class="tutorial-row">

                        <div class="font-medium text-sm">
                            {{ pathinfo($video, PATHINFO_FILENAME) }}
                        </div>

                        <div class="text-right">
                            <a
                                href="{{ Storage::disk('public')->url($video) }}"
                                target="_blank"
                            >
                                ▶ Tonton
                            </a>
                        </div>

                    </div>
                @endforeach

            </div>
        </div>

    </div>


    {{-- =========================
         CSS
    ========================== --}}
    <style>
        .expansion-title {
            margin-top: 20px !important;
        }

        .tutorial-table {
            width: 100%;
            border: 1px solid rgb(229 231 235);
            border-radius: 0.75rem;
            overflow: hidden;
        }

        .tutorial-row {
            display: grid;
            grid-template-columns: 1fr 120px;
            align-items: center;
            min-height: 35px;
            padding: 0 16px;
            border-bottom: 1px solid rgb(229 231 235);
            background: white;
        }

        .tutorial-row:last-child {
            border-bottom: none;
        }

        .tutorial-header {
            font-size: 0.875rem;
            font-weight: 600;
            background: rgb(249 250 251);
            color: rgb(75 85 99);
        }

        /* Dark mode */
        .dark .tutorial-table {
            border-color: rgb(55 65 81);
        }

        .dark .tutorial-row {
            background: rgb(17 24 39);
            border-color: rgb(55 65 81);
        }

        .dark .tutorial-header {
            background: rgb(31 41 55);
            color: rgb(209 213 219);
        }

        /* Mobile */
        @media (max-width: 640px) {
            .tutorial-row {
                grid-template-columns: 1fr 90px;
                padding: 0 10px;
                font-size: 0.875rem;
            }

            .tutorial-button {
                padding: 6px 9px;
                font-size: 0.75rem;
            }
        }
    </style>

</x-filament-panels::page>