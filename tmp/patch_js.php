<?php
$file = 'C:/Users/BREYNER/Herd/laravel_pb/resources/views/asesor/dashboard.blade.php';
$content = file_get_contents($file);

// 1. Update actualizarEstadoUI to handle inactivo
$oldUI = "        // ── Actualizar UI de estado ────────────────────────────────
        function actualizarEstadoUI(estado) {
            const badge     = document.getElementById('estado-badge');
            const textoEl   = document.getElementById('estado-texto');
            const btnAceptar = document.getElementById('btn-aceptar');
            const btnEspera  = document.getElementById('btn-espera');
            const btnEsperaTexto = document.getElementById('btn-espera-texto');

            const labels = { disponible: 'Disponible', en_espera: 'En Espera', ocupado: 'Ocupado' };
            textoEl.innerText = labels[estado] || estado;

            badge.className = `flex items-center gap-2 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest border-2 ` + (
                estado === 'disponible' ? 'bg-green-600 text-white border-green-600' :
                estado === 'en_espera'  ? 'bg-gray-400 text-white border-gray-400' :
                                          'bg-blue-600 text-white border-blue-600'
            );

            // Botón Aceptar
            if (estado === 'disponible') {
                btnAceptar.disabled = false;
                btnAceptar.classList.remove('opacity-40', 'cursor-not-allowed');
                btnAceptar.classList.add('pulse-blue');
            } else {
                btnAceptar.disabled = true;
                btnAceptar.classList.add('opacity-40', 'cursor-not-allowed');
                btnAceptar.classList.remove('pulse-blue');
            }

            // Botón Espera
            if (estado === 'ocupado') {
                btnEspera.disabled = true;
                btnEspera.classList.add('opacity-40', 'cursor-not-allowed');
            } else {
                btnEspera.disabled = false;
                btnEspera.classList.remove('opacity-40', 'cursor-not-allowed');
                if (estado === 'en_espera') {
                    btnEspera.className = btnEspera.className.replace('btn-warning', 'btn-primary');
                    btnEsperaTexto.innerText = 'Reanudar Actividad';
                } else {
                    btnEspera.className = btnEspera.className.replace('btn-primary', 'btn-warning');
                    btnEsperaTexto.innerText = 'Poner en Espera';
                }
            }
        }";

$newUI = "        // ── Variables de turno de trabajo ───────────────────────
        let sesInicioTimestamp = null; // Timestamp ISO de inicio de turno
        let timerInterval = null;

        function formatDuracion(segundos) {
            const h = Math.floor(segundos / 3600);
            const m = Math.floor((segundos % 3600) / 60);
            const s = segundos % 60;
            if (h > 0) return `\${h}h \${m}m`;
            if (m > 0) return `\${m}m \${s}s`;
            return `\${s}s`;
        }

        function iniciarTimerTurno(isoInicio) {
            sesInicioTimestamp = new Date(isoInicio).getTime();
            const timerEl = document.getElementById('turno-timer-texto');
            clearInterval(timerInterval);
            if (!timerEl) return;
            timerInterval = setInterval(() => {
                const diff = Math.floor((Date.now() - sesInicioTimestamp) / 1000);
                timerEl.innerText = formatDuracion(diff);
            }, 1000);
        }

        function detenerTimerTurno() {
            clearInterval(timerInterval);
            sesInicioTimestamp = null;
            const timerEl = document.getElementById('turno-timer-texto');
            if (timerEl) timerEl.innerText = '—';
        }

        // ── Actualizar UI de estado ────────────────────────────────
        function actualizarEstadoUI(estado, sesInicio = null) {
            const badge      = document.getElementById('estado-badge');
            const textoEl    = document.getElementById('estado-texto');
            const btnAceptar = document.getElementById('btn-aceptar');
            const btnEspera  = document.getElementById('btn-espera');
            const btnEsperaTexto  = document.getElementById('btn-espera-texto');
            const btnTurno   = document.getElementById('btn-turno');
            const timerDiv   = document.getElementById('turno-timer');
            const cardInactivo = document.getElementById('card-inactivo');

            const labels = { disponible: 'Disponible', en_espera: 'En Espera', ocupado: 'Ocupado', inactivo: 'Inactivo' };
            textoEl.innerText = labels[estado] || estado;

            badge.className = `flex items-center gap-2 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest border-2 ` + (
                estado === 'disponible' ? 'bg-green-600 text-white border-green-600' :
                estado === 'en_espera'  ? 'bg-gray-400 text-white border-gray-400' :
                estado === 'inactivo'   ? 'bg-gray-700 text-white border-gray-700' :
                                          'bg-blue-600 text-white border-blue-600'
            );

            // Botón Aceptar
            if (estado === 'disponible') {
                btnAceptar.disabled = false;
                btnAceptar.classList.remove('opacity-40', 'cursor-not-allowed');
                btnAceptar.classList.add('pulse-blue');
            } else {
                btnAceptar.disabled = true;
                btnAceptar.classList.add('opacity-40', 'cursor-not-allowed');
                btnAceptar.classList.remove('pulse-blue');
            }

            // Botón Espera
            if (estado === 'ocupado' || estado === 'inactivo') {
                btnEspera.disabled = true;
                btnEspera.classList.add('opacity-40', 'cursor-not-allowed');
            } else {
                btnEspera.disabled = false;
                btnEspera.classList.remove('opacity-40', 'cursor-not-allowed');
                if (estado === 'en_espera') {
                    btnEspera.className = btnEspera.className.replace('btn-warning', 'btn-primary');
                    btnEsperaTexto.innerText = 'Reanudar Actividad';
                } else {
                    btnEspera.className = btnEspera.className.replace('btn-primary', 'btn-warning');
                    btnEsperaTexto.innerText = 'Poner en Espera';
                }
            }

            // Botón Turno + cronómetro + aviso inactivo
            if (btnTurno) {
                if (estado === 'inactivo') {
                    btnTurno.onclick = iniciarTurno;
                    btnTurno.className = 'w-full bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold py-4 rounded-xl uppercase tracking-widest text-sm flex items-center justify-center gap-2 transition-all border-b-4 border-emerald-700 pulse-blue';
                    btnTurno.innerHTML = '<svg class=\"w-5 h-5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2.5\" d=\"M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728M12 8v4l3 3\"/></svg> ▶ Iniciar Turno';
                    if (timerDiv) timerDiv.classList.add('hidden');
                    if (cardInactivo) cardInactivo.classList.remove('hidden');
                    detenerTimerTurno();
                } else {
                    btnTurno.onclick = confirmarFinalizarTurno;
                    btnTurno.className = 'w-full bg-red-800 hover:bg-red-900 text-white font-extrabold py-4 rounded-xl uppercase tracking-widest text-sm flex items-center justify-center gap-2 transition-all border-b-4 border-red-950';
                    btnTurno.innerHTML = '<svg class=\"w-5 h-5\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2.5\" d=\"M21 12a9 9 0 11-18 0 9 9 0 0118 0z\"/><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2.5\" d=\"M9 10h6v4H9z\"/></svg> ■ Finalizar Turno';
                    if (timerDiv) timerDiv.classList.remove('hidden');
                    if (cardInactivo) cardInactivo.classList.add('hidden');
                    if (sesInicio && sesInicio !== sesInicioTimestamp) {
                        iniciarTimerTurno(sesInicio);
                    }
                }
            }
        }";

if (strpos($content, '// ── Actualizar UI de estado ────────────────────────────────') !== false) {
    $content = str_replace($oldUI, $newUI, $content);
    echo (strpos($content, 'iniciarTimerTurno') !== false) ? "UI update: OK\n" : "UI update: PARTIAL\n";
} else {
    echo "Section not found\n";
}

// 2. Add iniciarTurno / confirmarFinalizarTurno / finalizarTurno functions after aceptarPrioritarioModal
$oldAfterModal = "        async function aceptarPrioritarioModal() {
            cerrarRecordatorio();
            aceptarTurno();
        }";

$newAfterModal = "        async function aceptarPrioritarioModal() {
            cerrarRecordatorio();
            aceptarTurno();
        }

        // ── Iniciar Turno de Trabajo ───────────────────────────────
        async function iniciarTurno() {
            try {
                let res = await fetch('{{ route(\"asesor.turno.iniciar\") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                res = await handleFetchResponse(res);
                if (!res) return;
                const data = await res.json();
                if (!res.ok) { showToast(data.error || 'Error al iniciar turno.', 'error'); return; }
                actualizarEstadoUI('disponible', data.ses_inicio);
                showToast('Turno de trabajo iniciado. ¡Listo para atender!', 'success');
                await refreshPollData();
            } catch (e) { showToast('Error de conexión.', 'error'); }
        }

        function confirmarFinalizarTurno() {
            if (confirm('¿Estás seguro de que deseas finalizar tu turno de trabajo? Quedará registrada la hora de finalización.')) {
                finalizarTurno();
            }
        }

        async function finalizarTurno() {
            try {
                let res = await fetch('{{ route(\"asesor.turno.finalizar\") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                res = await handleFetchResponse(res);
                if (!res) return;
                const data = await res.json();
                if (!res.ok) { showToast(data.error || 'Error al finalizar turno.', 'error'); return; }
                document.getElementById('card-turno-actual').classList.add('hidden');
                actualizarEstadoUI('inactivo');
                showToast('Turno de trabajo finalizado.', 'warn');
                await refreshPollData();
            } catch (e) { showToast('Error de conexión.', 'error'); }
        }";

$content = str_replace($oldAfterModal, $newAfterModal, $content);
echo (strpos($content, 'asesor.turno.iniciar') !== false) ? "Turno functions: OK\n" : "Turno functions: FAIL\n";

// 3. Update refreshPollData to use ses_inicio
// After: actualizarEstadoUI('disponible'); in finalizarAtencion  
// AND update polling to propagate ses_inicio
$oldRefresh = "            } catch (e) {
                console.warn('Poll error:', e);
            }
        }";

// We need to inject ses_inicio handling in refreshPollData - find the poll function
// The safest: after firstLoad = false; let's update the polling to pass ses_inicio to actualizarEstadoUI

// Actually, the polling doesn't call actualizarEstadoUI directly - it just updates queues.
// We need to update ses_inicio from polling data. Let's find firstLoad = false;
$oldFirstLoad = "                firstLoad = false;

            } catch (e) {
                console.warn('Poll error:', e);
            }
        }";

$newFirstLoad = "                firstLoad = false;

                // Actualizar cronómetro de turno con datos del servidor
                if (data.ses_inicio) {
                    if (!sesInicioTimestamp) {
                        iniciarTimerTurno(data.ses_inicio);
                        const timerDiv = document.getElementById('turno-timer');
                        if (timerDiv) timerDiv.classList.remove('hidden');
                        const cardInactivo = document.getElementById('card-inactivo');
                        if (cardInactivo) cardInactivo.classList.add('hidden');
                    }
                } else {
                    detenerTimerTurno();
                }

            } catch (e) {
                console.warn('Poll error:', e);
            }
        }";

$content = str_replace($oldFirstLoad, $newFirstLoad, $content);
echo (strpos($content, 'Actualizar cronómetro de turno') !== false) ? "Poll timer: OK\n" : "Poll timer: FAIL\n";

file_put_contents($file, $content);
echo "Done.\n";
