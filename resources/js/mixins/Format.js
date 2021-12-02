export default {
  methods: {
    dateFormat(
      date,
      options = {
        timezome: "UTC",
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      },
      locales
    ) {
      if (!(date instanceof Date)) {
        date = new Date(date);
      }
      return new Intl.DateTimeFormat(locales, options).format(date);
    },
  },
};
