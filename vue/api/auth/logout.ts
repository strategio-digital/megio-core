/**
 * Copyright (c) 2022 Strategio Digital s.r.o.
 * @author Jiří Zapletal (https://strategio.digital, jz@strategio.digital)
 */

const logout = (): void => {
    localStorage.removeItem('strategio_saas_user')
}

export default logout