/**
 * MIRACLE SPA - Flujo Interactivo de Agendamiento de Citas (Wizard)
 */

document.addEventListener('DOMContentLoaded', () => {
    // Estado del proceso de reserva
    const state = {
        step: 1,
        selectedCategory: 'todas',
        selectedService: null,
        selectedDate: '',
        selectedTime: '',
        selectedProfessional: 'any', // 'any' o id numérico
        selectedProfessionalName: 'Cualquier especialista disponible',
        clientName: '',
        clientEmail: '',
        clientPhone: '',
        clientNotes: '',
        confirmedAppointment: null
    };

    // Referencias al DOM
    const stepTabs = document.querySelectorAll('[data-step-target]');
    const stepContents = document.querySelectorAll('.wizard-step-content');
    const serviceCards = document.querySelectorAll('.service-card');
    const categoryFilters = document.querySelectorAll('[data-category-filter]');
    const dateInput = document.getElementById('booking-date');
    const slotsContainer = document.getElementById('slots-container');
    const slotsLoading = document.getElementById('slots-loading');
    const slotsEmpty = document.getElementById('slots-empty');
    const professionalsContainer = document.getElementById('professionals-container');
    const bookingForm = document.getElementById('booking-form');
    const confirmBtn = document.getElementById('btn-confirm-booking');
    const btnNextToDate = document.getElementById('btn-next-to-date');
    const btnNextToTime = document.getElementById('btn-next-to-time');
    const btnNextToProf = document.getElementById('btn-next-to-prof');
    const btnNextToForm = document.getElementById('btn-next-to-form');

    // Resumen en la barra lateral
    const summaryServiceName = document.getElementById('summary-service-name');
    const summaryServiceDuration = document.getElementById('summary-service-duration');
    const summaryServicePrice = document.getElementById('summary-service-price');
    const summaryDate = document.getElementById('summary-date');
    const summaryTime = document.getElementById('summary-time');
    const summaryProf = document.getElementById('summary-professional');

    // Inicializar fecha mínima en el selector (hoy)
    if (dateInput) {
        const todayStr = new Date().toISOString().split('T')[0];
        dateInput.min = todayStr;
        dateInput.value = todayStr;
        state.selectedDate = todayStr;
    }

    // 1. Navegación entre pasos
    function goToStep(targetStep) {
        if (targetStep < 1 || targetStep > 6) return;
        state.step = targetStep;

        stepContents.forEach(content => {
            const stepNum = parseInt(content.dataset.step);
            if (stepNum === targetStep) {
                content.classList.remove('hidden');
            } else {
                content.classList.add('hidden');
            }
        });

        // Actualizar indicadores del wizard
        const bullets = document.querySelectorAll('.wizard-step-bullet');
        bullets.forEach((bullet, idx) => {
            const stepNum = idx + 1;
            bullet.classList.remove('active', 'completed', 'inactive');
            if (stepNum === targetStep) {
                bullet.classList.add('active');
            } else if (stepNum < targetStep) {
                bullet.classList.add('completed');
                bullet.innerHTML = '✓';
            } else {
                bullet.classList.add('inactive');
                bullet.innerHTML = stepNum;
            }
        });

        window.scrollTo({ top: document.getElementById('booking-wizard-section').offsetTop - 80, behavior: 'smooth' });
    }

    // 2. Filtro de Categorías de Servicio
    categoryFilters.forEach(btn => {
        btn.addEventListener('click', () => {
            categoryFilters.forEach(b => b.classList.remove('bg-[#12372a]', 'text-white'));
            categoryFilters.forEach(b => b.classList.add('bg-white', 'text-gray-700'));
            btn.classList.remove('bg-white', 'text-gray-700');
            btn.classList.add('bg-[#12372a]', 'text-white');

            const cat = btn.dataset.categoryFilter;
            state.selectedCategory = cat;

            serviceCards.forEach(card => {
                if (cat === 'todas' || card.dataset.category === cat) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    });

    // 3. Selección de Servicio
    serviceCards.forEach(card => {
        card.addEventListener('click', () => {
            serviceCards.forEach(c => c.classList.remove('border-[#12372a]', 'ring-2', 'ring-[#12372a]', 'bg-emerald-50/40'));
            card.classList.add('border-[#12372a]', 'ring-2', 'ring-[#12372a]', 'bg-emerald-50/40');

            state.selectedService = {
                id: parseInt(card.dataset.id),
                name: card.dataset.name,
                duration: parseInt(card.dataset.duration),
                price: parseFloat(card.dataset.price)
            };

            // Actualizar resumen
            if (summaryServiceName) summaryServiceName.textContent = state.selectedService.name;
            if (summaryServiceDuration) summaryServiceDuration.textContent = `${state.selectedService.duration} min`;
            if (summaryServicePrice) summaryServicePrice.textContent = `$${state.selectedService.price.toFixed(2)} USD`;

            if (btnNextToDate) btnNextToDate.disabled = false;
        });
    });

    if (btnNextToDate) {
        btnNextToDate.addEventListener('click', () => {
            if (state.selectedService) {
                goToStep(2);
                loadSlots();
            }
        });
    }

    // 4. Cambio de Fecha
    if (dateInput) {
        dateInput.addEventListener('change', (e) => {
            state.selectedDate = e.target.value;
            state.selectedTime = '';
            if (summaryDate) summaryDate.textContent = formatDateSpanish(state.selectedDate);
            if (summaryTime) summaryTime.textContent = 'Pendiente';
            if (btnNextToProf) btnNextToProf.disabled = true;
            loadSlots();
        });
    }

    if (btnNextToTime) {
        btnNextToTime.addEventListener('click', () => {
            goToStep(3);
            loadSlots();
        });
    }

    // 5. Carga de Horarios Disponibles vía AJAX
    async function loadSlots() {
        if (!state.selectedService || !state.selectedDate) return;

        if (slotsContainer) slotsContainer.innerHTML = '';
        if (slotsLoading) slotsLoading.classList.remove('hidden');
        if (slotsEmpty) slotsEmpty.classList.add('hidden');

        try {
            const resp = await fetch(`api/get_slots.php?date=${encodeURIComponent(state.selectedDate)}&service_id=${state.selectedService.id}`);
            const data = await resp.json();

            if (slotsLoading) slotsLoading.classList.add('hidden');

            if (!data.slots || data.slots.length === 0) {
                if (slotsEmpty) slotsEmpty.classList.remove('hidden');
                return;
            }

            renderSlots(data.slots);
        } catch (err) {
            if (slotsLoading) slotsLoading.classList.add('hidden');
            if (slotsEmpty) {
                slotsEmpty.classList.remove('hidden');
                slotsEmpty.textContent = 'Hubo un error al consultar disponibilidad. Intenta nuevamente.';
            }
        }
    }

    function renderSlots(slots) {
        if (!slotsContainer) return;
        slotsContainer.innerHTML = '';

        // Agrupar por periodo (manana, tarde, noche)
        const periods = {
            manana: { label: '🌅 Turno Mañana (09:00 - 12:00)', slots: [] },
            tarde: { label: '☀️ Turno Tarde (12:00 - 17:00)', slots: [] },
            noche: { label: '🌙 Turno Tarde / Crepúsculo (17:00 - 20:00)', slots: [] }
        };

        slots.forEach(s => {
            if (periods[s.period]) {
                periods[s.period].slots.push(s);
            }
        });

        Object.keys(periods).forEach(periodKey => {
            const group = periods[periodKey];
            if (group.slots.length === 0) return;

            const groupDiv = document.createElement('div');
            groupDiv.className = 'mb-6';
            groupDiv.innerHTML = `
                <div class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3 flex items-center gap-2">
                    ${group.label}
                    <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">${group.slots.length} turnos</span>
                </div>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2.5" id="group-${periodKey}"></div>
            `;
            slotsContainer.appendChild(groupDiv);

            const grid = groupDiv.querySelector(`#group-${periodKey}`);
            group.slots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'time-chip py-2.5 px-3 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-800 hover:border-[#12372a] text-center shadow-xs';
                btn.innerHTML = `
                    <div class="text-sm font-bold">${slot.formatted_time}</div>
                    <div class="text-[10px] text-emerald-700 font-medium">${slot.available_professionals_count} libre(s)</div>
                `;

                if (state.selectedTime === slot.time) {
                    btn.classList.add('selected');
                }

                btn.addEventListener('click', () => {
                    document.querySelectorAll('.time-chip').forEach(c => c.classList.remove('selected'));
                    btn.classList.add('selected');
                    state.selectedTime = slot.time;

                    if (summaryTime) summaryTime.textContent = slot.formatted_time;
                    if (summaryDate) summaryDate.textContent = formatDateSpanish(state.selectedDate);
                    if (btnNextToProf) btnNextToProf.disabled = false;

                    // Avanzar automáticamente a selección de profesional o habilitar
                    loadProfessionals();
                });

                grid.appendChild(btn);
            });
        });
    }

    if (btnNextToProf) {
        btnNextToProf.addEventListener('click', () => {
            if (state.selectedTime) {
                goToStep(4);
                loadProfessionals();
            }
        });
    }

    // 6. Carga de Especialistas Disponibles para el Slot Seleccionado
    async function loadProfessionals() {
        if (!professionalsContainer || !state.selectedService) return;

        professionalsContainer.innerHTML = '<div class="col-span-2 text-center py-6 text-gray-400">Consultando especialistas calificados...</div>';

        try {
            const url = `api/get_professionals.php?service_id=${state.selectedService.id}&date=${encodeURIComponent(state.selectedDate)}&time=${encodeURIComponent(state.selectedTime)}`;
            const resp = await fetch(url);
            const data = await resp.json();

            renderProfessionals(data.professionals || []);
        } catch (e) {
            professionalsContainer.innerHTML = '<div class="col-span-2 text-center py-6 text-rose-500">Error al cargar especialistas.</div>';
        }
    }

    function renderProfessionals(profs) {
        if (!professionalsContainer) return;
        professionalsContainer.innerHTML = '';

        // Opción 1: Asignación automática ("Cualquier profesional")
        const anyCard = document.createElement('div');
        anyCard.className = `prof-card p-4 rounded-xl border ${state.selectedProfessional === 'any' ? 'border-[#12372a] bg-emerald-50/50 ring-2 ring-[#12372a]' : 'border-gray-200 bg-white'} cursor-pointer hover:border-[#12372a] transition-all flex items-center gap-4`;
        anyCard.innerHTML = `
            <div class="w-12 h-12 rounded-full bg-[#12372a] text-[#d4af37] font-serif font-bold text-lg flex items-center justify-center shrink-0">
                ✨
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <h4 class="font-bold text-gray-900 text-sm">Cualquier Profesional Disponible</h4>
                    <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full">Recomendado</span>
                </div>
                <p class="text-xs text-gray-500 mt-0.5">Asignación inteligente del terapeuta con mayor disponibilidad para este horario.</p>
            </div>
            <div class="text-xs font-bold text-[#12372a]">Seleccionar</div>
        `;

        anyCard.addEventListener('click', () => {
            selectProf('any', 'Cualquier especialista disponible', anyCard);
        });
        professionalsContainer.appendChild(anyCard);

        // Opciones individuales
        profs.forEach(p => {
            const isAvail = (p.is_available_for_slot !== false);
            const card = document.createElement('div');
            card.className = `prof-card p-4 rounded-xl border ${state.selectedProfessional == p.id ? 'border-[#12372a] bg-emerald-50/50 ring-2 ring-[#12372a]' : 'border-gray-200 bg-white'} ${isAvail ? 'cursor-pointer hover:border-[#12372a]' : 'opacity-50 cursor-not-allowed bg-gray-50'} transition-all flex items-center gap-4`;

            const initials = p.name.split(' ').map(n => n[0]).join('').substring(0, 2);

            card.innerHTML = `
                <div class="w-12 h-12 rounded-full bg-[#12372a]/10 text-[#12372a] font-bold text-sm flex items-center justify-center shrink-0">
                    ${initials}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h4 class="font-bold text-gray-900 text-sm truncate">${p.name}</h4>
                        <span class="text-xs text-amber-500 font-semibold flex items-center gap-0.5">★ ${parseFloat(p.rating).toFixed(1)}</span>
                    </div>
                    <p class="text-xs text-gray-500 truncate">${p.title}</p>
                    ${!isAvail ? '<span class="text-[10px] text-rose-600 font-bold block mt-0.5">Ocupado en este horario</span>' : ''}
                </div>
                <div>
                    ${isAvail ? `<span class="text-xs font-bold text-[#12372a]">Elegir</span>` : `<span class="text-xs text-gray-400">No disp.</span>`}
                </div>
            `;

            if (isAvail) {
                card.addEventListener('click', () => {
                    selectProf(p.id, p.name, card);
                });
            }

            professionalsContainer.appendChild(card);
        });
    }

    function selectProf(id, name, element) {
        document.querySelectorAll('.prof-card').forEach(c => c.classList.remove('border-[#12372a]', 'bg-emerald-50/50', 'ring-2', 'ring-[#12372a]'));
        element.classList.add('border-[#12372a]', 'bg-emerald-50/50', 'ring-2', 'ring-[#12372a]');

        state.selectedProfessional = id;
        state.selectedProfessionalName = name;

        if (summaryProf) summaryProf.textContent = name;
        if (btnNextToForm) btnNextToForm.disabled = false;
    }

    if (btnNextToForm) {
        btnNextToForm.addEventListener('click', () => {
            goToStep(5);
        });
    }

    // 7. Enviar Reserva y Confirmación
    if (bookingForm) {
        bookingForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            state.clientName = document.getElementById('client-name').value.trim();
            state.clientEmail = document.getElementById('client-email').value.trim();
            state.clientPhone = document.getElementById('client-phone').value.trim();
            state.clientNotes = document.getElementById('client-notes').value.trim();

            if (!state.clientName || !state.clientEmail || !state.clientPhone) {
                alert('Por favor completa todos los campos de contacto obligatorios.');
                return;
            }

            if (!confirmBtn) return;
            const originalText = confirmBtn.innerHTML;
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                Confirmando tu Reserva...
            `;

            try {
                const payload = {
                    service_id: state.selectedService.id,
                    date: state.selectedDate,
                    start_time: state.selectedTime,
                    professional_id: state.selectedProfessional,
                    client_name: state.clientName,
                    client_email: state.clientEmail,
                    client_phone: state.clientPhone,
                    client_notes: state.clientNotes
                };

                const resp = await fetch('api/book.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const result = await resp.json();

                if (result.success) {
                    state.confirmedAppointment = result.appointment;
                    renderConfirmationScreen(result);
                    goToStep(6);
                } else {
                    alert(result.error || 'No se pudo completar la reserva.');
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = originalText;
                }
            } catch (err) {
                alert('Error de conexión con el servidor de reservas.');
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = originalText;
            }
        });
    }

    // 8. Renderizado de Pantalla de Confirmación de Reserva
    function renderConfirmationScreen(result) {
        const app = result.appointment;
        const codeElem = document.getElementById('confirmed-code');
        const serviceElem = document.getElementById('confirmed-service');
        const profElem = document.getElementById('confirmed-professional');
        const dateElem = document.getElementById('confirmed-date');
        const timeElem = document.getElementById('confirmed-time');
        const priceElem = document.getElementById('confirmed-price');
        const emailElem = document.getElementById('confirmed-email');
        const manageLink = document.getElementById('confirmed-manage-link');

        if (codeElem) codeElem.textContent = result.code;
        if (serviceElem) serviceElem.textContent = app.service_name;
        if (profElem) profElem.textContent = app.professional_name;
        if (dateElem) dateElem.textContent = formatDateSpanish(app.date);
        if (timeElem) timeElem.textContent = `${app.start_time.substring(0, 5)} - ${app.end_time.substring(0, 5)}`;
        if (priceElem) priceElem.textContent = `$${parseFloat(app.price).toFixed(2)} USD`;
        if (emailElem) emailElem.textContent = app.client_email;

        if (manageLink) {
            manageLink.href = `gestionar-cita.php?token=${encodeURIComponent(result.manage_token)}`;
        }
    }

    function formatDateSpanish(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        const date = new Date(parts[0], parts[1] - 1, parts[2]);
        const dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        return `${dias[date.getDay()]}, ${date.getDate()} de ${meses[date.getMonth()]} de ${date.getFullYear()}`;
    }

    // Exponer goToStep globalmente para botones "Volver"
    window.goToWizardStep = goToStep;
});
