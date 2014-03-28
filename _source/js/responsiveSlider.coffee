class ResponsiveSlider
  # default params
  element:      'defaultValue'
  radios:       'defaultValue'
  currentIndex: 0
  nIntervalId:  'defaultValue'
  nextButton:   null
  previousButton: null
  interval:     5000

  # define params with passed config
  constructor: (params) ->
    _this = this

    for key,value of params
      if _this[key]
        DEBUG && console.log "ResponsiveSlider: constructor() { define '#{key}' as '#{value}' }"
      else
        DEBUG && console.log "ResponsiveSlider: constructor() { param '#{key}' doesn't exist }"

      # define _this with params
      _this[key] = value

    # buttons
    if _this.nextButton
      _this.nextButton.addEventListener "click", (event) => _this.clickHandler('next')

    if _this.previousButton
      _this.previousButton.addEventListener "click", (event) => _this.clickHandler('previous')

    # slider
    _this.element.addEventListener "mouseenter", (event) => _this.action('stop')
    _this.element.addEventListener "mouseleave", (event) => _this.action('start')


  # do action - start | stop
  action: (action) ->
    _this = this

    switch action
      when "start"
        DEBUG && console.log "ResponsiveSlider: action() { action is '#{action}' }"
        _this.nIntervalId = window.setInterval(() ->
          _this.gotoItem()
        , _this.interval)
        return

      # catch stop and everything else
      else
        DEBUG && console.log "ResponsiveSlider: action() { action is '#{action}' }"
        clearInterval(_this.nIntervalId)


  # goto something
  gotoItem: (index, direction) ->
    _this = this

    if not direction
      DEBUG && console.log "ResponsiveSlider: gotoItem() { direction is 'undefined', default to 'next'}"
      direction = 'next'

    if index isnt 0
      if not index
        DEBUG && console.log "ResponsiveSlider: gotoItem() { index is 'undefined', getting direction: '#{direction}' item }"
        index = _this.getNextIndex(direction)

    if index is -1 or index is _this.radios.length + 1
      DEBUG && console.log "ResponsiveSlider: gotoItem() { index '#{index}' doesn't exist, default to 0 }"
      index = 0

    DEBUG && console.log "ResponsiveSlider: gotoItem() { set 'radio[#{index}]' to checked }"
    _this.radios[index].checked=true


  # get next item
  getNextIndex: (direction) ->
    _this = this

    switch direction
      when 'previous'
        nextIndexNumber = _this.currentIndex - 1

        if nextIndexNumber is -1
          DEBUG && console.log "Responsiveslider: getNextIndex() { first slide reached, goto last slide }"
          nextIndexNumber = _this.radios.length - 1

      # catch next and anything else
      else
        nextIndexNumber = _this.currentIndex + 1

        if nextIndexNumber is _this.radios.length
          DEBUG && console.log "Responsiveslider: getNextIndex() { last slide reached, goto first slide }"
          nextIndexNumber = 0

    _this.currentIndex = nextIndexNumber
    return nextIndexNumber

  # click handler
  clickHandler: (direction) ->
    _this = this

    if not direction
      DEBUG && console.log "ResponsiveSlider: gotoItem() { direction is 'undefined', default to 'next'}"
      direction = 'next'

    _this.action('stop')
    _this.gotoItem(_this.getNextIndex(direction), direction)