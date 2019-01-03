export default {
  /**------------------------------------------
   * get page number from url if provided
   ------------------------------------------*/
  getPageQuery() {
    let _ = window._;
    let queryString = _.chain(window.location)
                       .split('?', '2')
                       .value();

    if (typeof queryString[1] !== undefined) {
      queryString = _.chain(queryString[1])
        .split('&')
        .map(_.partial(_.split, _, '=', 2))
        .fromPairs()
        .value();
    }

    let page = 1;
    if (typeof queryString.page !== undefined && !isNaN(queryString.page)) {
      page = parseInt(queryString.page);
    }

    return page;
  },
}
