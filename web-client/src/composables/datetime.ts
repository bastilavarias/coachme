import { format, formatDistanceToNow, addMinutes, isToday } from 'date-fns';

export function useDatetime() {
    return {
        formatTime(time) {
            return format(new Date(`2000-01-01T${time}`), 'h:mma');
        },

        formatDate(date: Date, type: string = 'backend_friendly') {
            if (type === 'backend_friendly') {
                return format(new Date(date), 'yyyy-MM-dd');
            }
            if (type === 'formatted') {
                return format(new Date(date), 'MMMM dd, yyyy');
            }
            if (type === 'distance') {
                return formatDistanceToNow(date, { addSuffix: true });
            }
            return date;
        },

        today(date: Date) {
            return isToday(new Date(date));
        },

        generateTimeslots() {
            const timeSlots = [];
            let startTime = new Date();
            const endTime = new Date();
            startTime.setHours(9, 0, 0, 0); // Set end time to 9:00 AM
            endTime.setHours(22, 0, 0, 0); // Set end time to 10:00 PM
            while (startTime < endTime) {
                const formattedTime = format(startTime, 'h:mma');
                const formattedValue = format(startTime, 'HH:mm:ss');
                timeSlots.push({ text: formattedTime, value: formattedValue });
                startTime = addMinutes(startTime, 60); // Add 60 minutes for each time slot
            }

            return timeSlots;
        },
    };
}
