/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

const endpoint = import.meta.env.DEV ? 'http://localhost:8090/api' : '/api'

const fetchApi = () => {
    console.log('Fetch:', endpoint)
}

export default {
    fetch: fetchApi
}